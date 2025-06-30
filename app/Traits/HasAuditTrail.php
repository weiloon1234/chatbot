<?php

namespace App\Traits;

use App\Models\AuditTrail;

trait HasAuditTrail
{
    abstract public function getAuditTrailDescription(): string;

    public static function bootHasAuditTrail()
    {
        static::created(function ($q) {
            if (! $q->disabled_audit_trail && defined('IN_ADMIN') && IN_ADMIN === true) {
                $model = new AuditTrail;
                $model->admin_id = request()->user()->id;
                $model->model_class = get_class($q);
                $model->model_id = $q->id;
                $model->created_data = $q;
                $model->operation = 'CREATED';
                $model->description = request()->user()->username.' created '.$q->getAuditTrailDescription();
                $model->save();
            }
        });

        static::updated(function ($q) {
            if (! $q->disabled_audit_trail && defined('IN_ADMIN') && IN_ADMIN === true) {
                $model = new AuditTrail;
                $model->admin_id = request()->user()->id;
                $model->model_class = get_class($q);
                $model->model_id = $q->id;
                $model->old_data = $q->getOriginal();
                $model->edited_data = $q->getDirty();
                $model->operation = 'EDITED';
                $model->description = request()->user()->username.' edited '.$q->getAuditTrailDescription();
                $model->save();
            }
        });

        static::deleted(function ($q) {
            if (! $q->disabled_audit_trail && defined('IN_ADMIN') && IN_ADMIN === true) {
                $model = new AuditTrail;
                $model->admin_id = request()->user()->id;
                $model->model_class = get_class($q);
                $model->model_id = $q->id;
                $model->old_data = $q->getOriginal();
                $model->operation = 'DELETED';
                $model->description = request()->user()->username.' deleted '.$q->getAuditTrailDescription();
                $model->save();
            }
        });
    }
}
