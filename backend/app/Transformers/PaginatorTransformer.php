<?php

namespace App\Transformers;

/**
 * @property \Illuminate\Pagination\LengthAwarePaginator $resource
 */
class PaginatorTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'has_more_pages' => $this->resource->hasMorePages(),
            'next_page_url' => $this->resource->nextPageUrl(),
            'current_page' => $this->resource->currentPage(),
            'next_page' => $this->resource->currentPage() + 1,
            'per_page' => $this->resource->perPage(),
            'total' => $this->resource->total(),
            'total_page' => $this->resource->lastPage(),
            'count' => $this->resource->count(),
        ];
    }
}
