<?php

namespace App\Traits;

use App\Models\Galleries;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HasGalleries
{
    use UploadFile;

    public function fileColumns()
    {
        return $this->fileColumns();
    }

    /**
     * Get the attachments relation morphed to the current model class
     *
     * @return MorphMany
     */
    public function galleries()
    {
        return $this->morphMany(get_class(app(Galleries::class)), 'model');
    }

    /**
     * Find an attachment by key
     *
     * @return Galleries|null
     */
    public function gallery(string $uuid)
    {
        return $this->galleries->where('uuid', $uuid)->first();
    }

    /**
     * Find all attachments with the given
     *
     * @param  string  $group
     * @return Galleries[]|Collection
     */
    public function attachmentsGroup($group)
    {
        return $this->galleries->where('group', $group);
    }

    /**
     * @param  UploadedFile|string  $fileOrPath
     * @param  array  $options  Set attachment options : uuid, group
     * @return Galleries|null
     *
     * @throws \Exception
     */
    public function attach($fileOrPath, $options = [])
    {
        if (! is_array($options)) {
            throw new \Exception('Attachment options must be an array');
        }

        if (empty($fileOrPath)) {
            throw new \Exception('Attached file is required');
        }

        $attributes = Arr::only($options, ['uuid', 'group', 'params', 'key']);

        if (! empty($attributes['key']) && $gallery = $this->attachments()->where('key', $attributes['key'])->first()) {
            $gallery->delete();
        }

        /** @var Galleries $gallery */
        $gallery = new Galleries;
        $gallery->uuid = \Str::uuid();
        $gallery->fill($attributes);
        $gallery->image = $fileOrPath;

        if ($gallery = $this->galleries()->save($gallery)) {
            return $gallery;
        }

        return null;
    }
}
