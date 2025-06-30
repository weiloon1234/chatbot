<?php

namespace App\DataTables;

use App\Exports\CollectionExportFromCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as NormalCollection;

abstract class BaseDataTable
{
    public int $total_records = 0;

    public int $total_pages = 0;

    public int $ipp = 30;

    protected string $class = '';

    protected mixed $model = null;

    protected Collection|NormalCollection $records;

    protected string $sorted_column = 'id';

    protected string $sorted = 'DESC';

    protected array $default_export_ignore_column = [
        'actions', 'action',
    ];

    protected array $default_timestamp_column = ['created_at', 'updated_at'];

    public function __construct()
    {
        if (request()->filled('ipp')) {
            $this->ipp = request()->get('ipp');
        }

        $this->model = $this->getQuery();

        $this->queryFilter();

        $this->querySorting();

        $this->queryCount();

        if (request()->get('export') == 1) {
        } else {
            $this->queryFetch(request()->get('p', 1));

            $this->transform();
        }
    }

    // COUNT PAGE AND OTHER DATA
    protected function queryCount()
    {
        $this->total_records = $this->model->count();
        if ($this->total_records > $this->ipp) {
            $this->total_pages = ceil($this->total_records / $this->ipp);
        } else {
            $this->total_pages = 1;
        }
    }

    // FETCH, FINALLY
    protected function queryFetch($page)
    {
        $this->records = $this->model->skip(($page - 1) * $this->ipp)->take($this->ipp)->get();
    }

    protected function queryFilter()
    {
        /**
         * AUTO QUERY FILTER BY COLUMN NAME
         * Patterns:
         * - f-like-<col>
         * - f-has-like-<relation>-<col>
         * - f-has-<relation>-<col>
         * - f-date-from-<col>
         * - f-date-to-<col>
         * - f-date-<col>
         * - f-gte-<col>
         * - f-lte-<col>
         * - f-locale-<col>
         * - f-locale-like-<col>
         * - f-locale-has-<relation>-<col>
         * - f-locale-has-like-<relation>-<col>
         * - f-like-any-<col1|col2|...>
         * - f-any-<col1|col2|...>
         * - f-has-like-any-<relation>-<col1|col2|...>
         * - f-has-any-<relation>-<col1|col2|...>
         * - f-<col>
         */
        foreach (request()->all() as $field => $var) {
            if (! is_array($var) && mb_strlen($var) > 0) {
                if (preg_match('/^f-(like-any|any|has-like-any|has-any|like|has-like|has|date-from|date-to|date|gte|lte|locale(?:-like)?(?:-has(?:-like)?)?)-(.+)$/', $field, $matches)) {
                    $type = $matches[1];
                    $rest = $matches[2];

                    switch ($type) {
                        case 'like-any':
                            $columns = explode('|', $rest);
                            $this->model = $this->model->where(function ($q) use ($columns, $var) {
                                foreach ($columns as $col) {
                                    $q->orWhere($col, 'LIKE', '%'.$var.'%');
                                }
                            });
                            break;

                        case 'any':
                            $columns = explode('|', $rest);
                            $this->model = $this->model->where(function ($q) use ($columns, $var) {
                                foreach ($columns as $col) {
                                    $q->orWhere($col, '=', $var);
                                }
                            });
                            break;

                        case 'has-like-any':
                            [$relation, $cols] = explode('-', $rest, 2);
                            $columns = explode('|', $cols);
                            $this->model = $this->model->whereHas($relation, function ($q) use ($columns, $var) {
                                $q->where(function ($query) use ($columns, $var) {
                                    foreach ($columns as $col) {
                                        $query->orWhere($col, 'LIKE', '%'.$var.'%');
                                    }
                                });
                            });
                            break;

                        case 'has-any':
                            [$relation, $cols] = explode('-', $rest, 2);
                            $columns = explode('|', $cols);
                            $this->model = $this->model->whereHas($relation, function ($q) use ($columns, $var) {
                                $q->where(function ($query) use ($columns, $var) {
                                    foreach ($columns as $col) {
                                        $query->orWhere($col, '=', $var);
                                    }
                                });
                            });
                            break;

                        case 'like':
                            $this->model = $this->model->where($rest, 'LIKE', '%'.$var.'%');
                            break;

                        case 'has-like':
                            [$relation, $col] = explode('-', $rest, 2);
                            $this->model = $this->model->whereHas($relation, function ($q) use ($col, $var) {
                                $q->where($col, 'LIKE', '%'.$var.'%');
                            });
                            break;

                        case 'has':
                            [$relation, $col] = explode('-', $rest, 2);
                            $this->model = $this->model->whereHas($relation, function ($q) use ($col, $var) {
                                $q->where($col, '=', $var);
                            });
                            break;

                        case 'date-from':
                            $this->applyDateFrom($rest, $var);
                            break;

                        case 'date-to':
                            $this->applyDateTo($rest, $var);
                            break;

                        case 'date':
                            $this->applyDateExact($rest, $var);
                            break;

                        case 'gte':
                            $this->model = $this->model->where($rest, '>=',
                                \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $var, request()->header('TIMEZONE'))
                                    ->setTimezone(config('app.timezone'))
                            );
                            break;

                        case 'lte':
                            $this->model = $this->model->where($rest, '<=',
                                \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $var, request()->header('TIMEZONE'))
                                    ->setTimezone(config('app.timezone'))
                            );
                            break;

                        default:
                            if (preg_match('/^locale(?:-(like))?(?:-has(?:-(like))?)?-(.+)$/', $type.'-'.$rest, $localeMatches)) {
                                $this->applyLocaleFilter($localeMatches, $var);
                            }
                            break;
                    }

                } elseif (preg_match('/^f-(.+)$/', $field, $matches)) {
                    // fallback: f-<col>
                    $this->model = $this->model->where($matches[1], '=', $var);
                }
            }
        }

        if (method_exists($this, 'filters')) {
            $this->filters();
        }
    }

    protected function applyDateFrom($col, $var)
    {
        $d = \Carbon\Carbon::createFromFormat('Y-m-d', $var, request()->header('TIMEZONE'))->setTime(0, 0, 0);
        $this->model = $this->model->where($col, '>=', $d->setTimezone(config('app.timezone')));
    }

    protected function applyDateTo($col, $var)
    {
        $d = \Carbon\Carbon::createFromFormat('Y-m-d', $var, request()->header('TIMEZONE'))->endOfDay();
        $this->model = $this->model->where($col, '<=', $d->setTimezone(config('app.timezone')));
    }

    protected function applyDateExact($col, $var)
    {
        $d = \Carbon\Carbon::createFromFormat('Y-m-d', $var, request()->header('TIMEZONE'))->setTimezone(config('app.timezone'));
        $this->model = $this->model
            ->where($col, '>=', $d->copy()->format('Y-m-d').' 00:00:00')
            ->where($col, '<=', $d->copy()->format('Y-m-d').' 23:59:59');
    }

    protected function applyLocaleFilter($matches, $var)
    {
        // Example pattern match groups:
        // [1] => like (optional)
        // [2] => like in has (optional)
        // [3] => rest of field

        $like = $matches[1] ?? null;
        $hasLike = $matches[2] ?? null;
        $rest = $matches[3];

        $params = explode('-', $rest);

        if (! $like && ! $hasLike && count($params) == 1) {
            // f-locale-<name>
            $this->model = $this->model->where($params[0], '=', $var);
        } elseif ($like && count($params) == 1) {
            // f-locale-like-<name>
            $this->model = $this->model->where(function ($q) use ($params, $var) {
                foreach (loopLanguageForColumn($params[0]) as $col) {
                    $q->orWhere($col, 'LIKE', '%'.$var.'%');
                }
            });
        } elseif (! $like && ! $hasLike && count($params) >= 2) {
            // f-locale-has-<relation>-<name>
            $relation = $params[0];
            $col = $params[1];
            $this->model = $this->model->whereHas($relation, function ($q) use ($col, $var) {
                foreach (loopLanguageForColumn($col) as $c) {
                    $q->orWhere($c, '=', $var);
                }
            });
        } elseif ($hasLike && count($params) >= 2) {
            // f-locale-has-like-<relation>-<name>
            $relation = $params[0];
            $col = $params[1];
            $this->model = $this->model->whereHas($relation, function ($q) use ($col, $var) {
                foreach (loopLanguageForColumn($col) as $c) {
                    $q->orWhere($c, 'LIKE', '%'.$var.'%');
                }
            });
        }
    }

    protected function querySorting()
    {
        if (request()->filled('sorting_column')) {
            if (method_exists($this, 'getUnSortable')) {
                if (! in_array(request()->get('sorting_column'), $this->getUnSortable())) {
                    $this->sorted_column = request()->get('sorting_column');
                }
            } else {
                $this->sorted_column = request()->get('sorting_column');
            }
        }

        if (in_array(mb_strtoupper(request()->get('sorting')), ['DESC', 'ASC'])) {
            $this->sorted = mb_strtoupper(request()->get('sorting'));
        }

        $this->model = $this->model->orderBy($this->sorted_column, $this->sorted);
    }

    protected function transform(): void
    {
        if (request()->get('export') == 1 || method_exists($this, 'mappings')) {
            for ($i = 0; $i < $this->records->count(); $i++) {
                if (request()->get('export') == 1) {
                    foreach ($this->default_timestamp_column as $column) {
                        $this->records[$i][$column] = $this->records[$i][$column]->timezone(request()->header('TIMEZONE'));
                    }
                }

                if (method_exists($this, 'mappings')) {
                    foreach ($this->mappings() as $column => $cb) {
                        $this->records[$i][$column] = $cb($this->records[$i]);
                    }
                }
            }
        }
    }

    public function getExport()
    {
        $per_page = 5000;

        $rows = collect();
        $header_columns = [];
        $body_columns = [];

        $headers = json_decode(request()->get('headers'), true);
        foreach ($headers as $header) {
            if (! isset($header['export_ignore']) || (isset($header['export_ignore']) && $header['export_ignore'] != true)) {
                $column_name = $header['export_column'] ?? $header['column'];

                if (! in_array($column_name, $this->default_export_ignore_column)) {
                    $header_columns[] = $header['label'];
                    $body_columns[] = $column_name;
                }
            }
        }

        $this->model->chunk($per_page, function ($records) use ($rows, $body_columns) {
            $this->records = $records;

            $this->transform();

            foreach ($this->records as $r) {
                $rows->add($r->only($body_columns));
            }
        });

        $c = new CollectionExportFromCollection($rows, $header_columns, $body_columns);

        return $c->download(request()->filled('export_file_name') ? request()->get('export_file_name') : now()->timestamp.'.csv');
    }

    public function getRecords(): Collection
    {
        return $this->records;
    }

    abstract public function getQuery(): Model|Builder|Collection|null;

    abstract public function getUnSortable(): array;

    abstract public function mappings(): array;

    abstract public function filters(): void;

    abstract public function permissions(): array;
}
