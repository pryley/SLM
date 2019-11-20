<?php

namespace App;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    /**
     * @param string $event
     * @param bool $halt
     * @return void
     */
    public function fireEvent($event, $halt = false)
    {
        $this->fireModelEvent($event, $halt);
    }
}
