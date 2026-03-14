<?php

namespace Illuminate\Contracts\Pagination {

    interface LengthAwarePaginator extends Paginator
    {
        /**
         * @param  string|array|null  $appends
         * @return $this
         */
        public function withQueryString();

        /**
         * @return $this
         */
        public function through(callable $callback);
    }
}
