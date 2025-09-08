<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasSortablePosition
{
    public function moveUp($category_id = null)
    {
        $above = static::where('pos', '<', $this->pos)
                        ->when($this->category_id, function ($query) {
                            $query->where('product_category_id', $this->category_id );
                        })
                        ->orderBy('pos', 'desc')
                        ->first();

        if ($above) {
            $this->swapPositions($above);
        }
    }

    public function moveDown()
    {
        $below = static::where('pos', '>', $this->pos)
                        ->orderBy('pos', 'asc')
                        ->first();

        if ($below) {
            $this->swapPositions($below);
        }
    }

    protected function swapPositions(Model $other)
    {
        $temp = $this->pos;
        $this->pos = $other->pos;
        $other->pos = $temp;

        $this->save();
        $other->save();
    }
}
