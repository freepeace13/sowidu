<?php

namespace App\Repositories;

use App\Contracts\Attachment\Attachable;
use App\Models\Attachment;
use App\Support\Facades\Registrars\Attachable as AttachableRegistrar;
use Illuminate\Database\Eloquent\Model;

class AttachmentRepository
{
    /**
     * Find the doctype of the given model.
     *
     * @param  App\Contracts\Attachment\Attachable  $model
     * @return string
     */
    public function findDoctypeOf(Attachable $model)
    {
        return AttachableRegistrar::collection()->search(get_class($model));
    }

    /**
     * Find the class of the given doctype.
     *
     * @return string
     */
    public function findClassOf(string $doctype)
    {
        $collection = AttachableRegistrar::collection()->flip();

        return $collection->search($doctype);
    }

    /**
     * Create new attachment of parent to child model if not exist.
     *
     * @param  Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function attach(Attachable $ref, array $children = [])
    {
        $attachedItems = collect();
        $children = $this->resolveAttachments($children);

        AttachableRegistrar::validateAttachableModels(...$children);

        foreach ($children as $refs) {
            if (!$this->checkHasDoc($ref, $refs)) {
                Attachment::query()->create([
                    'child_id' => $refs->getKey(),
                    'child_type' => $refs->getMorphClass(),
                    'parental_id' => $ref->getKey(),
                    'parental_type' => $ref->getMorphClass(),
                ]);

                $attachedItems->push($refs);
            }
        }

        return $attachedItems;
    }

    /**
     * Detach children attachments from the given parent model.
     *
     * @param  Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function detach(Attachable $ref, array $children = [])
    {
        $children = $this->resolveAttachments($children);

        AttachableRegistrar::validateAttachableModels(...$children);

        foreach ($children as $refs) {
            $attachments = $this->getAttachments($ref);

            if ($attachment = $this->findDocAttachmentFrom($attachments, $refs)) {
                $attachment->delete();
            }
        }

        return $this->getAttachedDocs($ref);
    }

    /**
     * Synchronize given children docs to the parent.
     *
     * @param  Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function sync(Attachable $ref, array $children = [])
    {
        foreach ($this->getAttachments($ref) as $attachment) {
            $attachment->delete();
        }

        return $this->attach($ref, $children);
    }

    /**
     * Determine the given attachments collection values has child exactly
     * the same as the given refs.
     *
     * @param  Illuminate\Support\Collection  $attachments
     * @param  App\Contracts\Attachment\Attachable  $refs
     * @return bool
     */
    public function findDocAttachmentFrom(Collection $attachments, Attachable $refs)
    {
        return $attachments->first(function ($value) use ($refs) {
            return $value->child->is($refs);
        });
    }

    /**
     * Determine reference attachments contains the same with the given refs.
     *
     * @param  App\Contracts\Attachment\Attachable  $ref
     * @param  App\Contracts\Attachment\Attachable  $refs
     * @return bool
     */
    public function checkHasDoc(Attachable $ref, Attachable $refs)
    {
        return $this->getAttachedDocs($ref)->contains(function ($ref) use ($refs) {
            return $ref->is($refs);
        });
    }

    /**
     * Get all attachments by type base on the given class.
     *
     * @param  App\Contracts\Attachment\Attachable  $ref
     * @return Illuminate\Support\Collection
     */
    public function getAllTypeOf(Attachable $ref, string $refsClass)
    {
        return $this->getAttachedDocs($ref)->filter(function ($item) use ($refsClass) {
            return get_class($item) === $refsClass;
        });
    }

    /**
     * Get all attachemnts of the given reference.
     *
     * @param  App\Contracts\Attachment\Attachable  $ref
     * @return Illuminate\Support\Collection
     */
    public function getAttachedDocs(Attachable $ref)
    {
        return $this->getAttachments($ref)->map(function ($item) use ($ref) {
            return $item->child->is($ref) ? $item->parent : $item->child;
        });
    }

    /**
     * Get the attachments that the given model attached as parent.
     *
     * @param  Illuminate\Database\Eloquent\Model  $parent
     * @return Illuminate\Support\Collection
     */
    public function getAttachments(Attachable $ref)
    {
        return Attachment::query()
            ->where(function ($query) use ($ref) {
                $query->where('parental_id', $ref->getKey());
                $query->where('parental_type', $ref->getMorphClass());
            })
            ->orWhere(function ($query) use ($ref) {
                $query->where('child_id', $ref->getKey());
                $query->where('child_type', $ref->getMorphClass());
            })
            ->get();
    }

    /**
     * Resolve attachment classes.
     *
     * @return array
     */
    public function resolveAttachments(array $attachments)
    {
        return collect($attachments)->map(function ($doc) {
            return $this->resolve($doc);
        })->all();
    }

    /**
     * Resolve the given doc type and id.
     *
     * @return App\Contracts\Attachment\Attachable
     */
    public function resolve(array $doc)
    {
        $class = $this->findClassOf($doc['doc_type']);

        return $class::find($doc['id']);
    }
}
