<?php

namespace App\Utils\Kmeans;


class Centroid extends Document
{
    /**
     * Create a new centroid with custom words
     *
     * This method creates a centroid by initializing a document with the provided words.
     * It sets the words for the document and returns it as a centroid.
     *
     * @param array $words The words to set for the centroid
     * @return static The created centroid with custom words
     */
    public static function createWithCustomWords($words)
    {
        $document = new static('', null);
        $document->setWords($words);

        return $document;
    }

    /**
     * Create a centroid from a collection of documents
     *
     * This method initializes the centroids by taking $k random documents from the collection.
     * You can replace this with a more sophisticated method if needed.
     *
     * @param \Illuminate\Support\Collection $documents The collection of documents
     * @param int $k The number of centroids to create
     * @return static The created centroids
     */
    public static function createCentroid($documents, int $k = 2)
    {
        // Take $k random documents from the collection
        // This is a simple way to initialize the centroids
        // You can replace this with a more sophisticated method if needed
        return $documents->shuffle()->take($k)->map(function (Document $document) {
            $centroid = new static($document->sentences);
            $centroid->setIndex($document->index());
            $centroid->countDocumentWords($document->getUniqueWords());

            return $centroid;
        });
    }

    /**
     * Compare two collections of centroids
     *
     * This method compares two collections of centroids and returns true if they are different and false if they are the same.
     *
     * @param \Illuminate\Support\Collection<Centroid> $centroids The first collection of centroids
     * @param \Illuminate\Support\Collection<Centroid> $otherCentroids The second collection of centroids
     * @return bool True if the centroids are different, false if they are the same
     */
    public static function compareCentroids($centroids, $otherCentroids)
    {
        if ($centroids->count() != $otherCentroids->count()) {
            throw new \InvalidArgumentException('Centroids count must be equal');
        }

        // Iterate over each centroid in the first collection
        foreach ($centroids as $index => $centroid) {
            // Check if the centroid is not same as the centroid in the second collection
            if (!$centroid->is($otherCentroids[$index])) {
                return false;
            }
        }

        // If the centroids are different, return true
        return true;
    }

    /**
     * Recalculate the centroid's word values based on its members
     *
     * @param array $members The members of the centroid
     * @return void
     */
    public function recalculateCentroid($members)
    {
        // Get the count of the members
        $count = count($members);

        // Iterate over each unique word in the document
        foreach ($this->getUniqueWords() as $uniqueWord) {
            // Calculate the sum of the word's values in all the members
            $sum = array_reduce($members, fn ($initial, Document $document) => $initial + $document->getValue($uniqueWord), 0);

            // Calculate the new value of the word
            $this->words[$uniqueWord] = $sum == 0 ? 0 : $sum / $count;
        }
    }
}
