<?php

namespace App\Utils\Kmeans;

class Kmeans
{
    /**
     * @var \Illuminate\Support\Collection<Document> $documents
     */
    private $documents;

    /**
     * @var \Illuminate\Support\Collection<Centroid> $centroids
     */
    private $centroids;

    /**
     * @var int
     */
    private $k;

    /**
     * @var array
     */
    private $clusters;

    /**
     * @var array
     */
    private $uniqueWordLists;

    /**
     * Initialize the K-Means clustering
     *
     * @param array|\Illuminate\Support\Collection $documents
     * @param int $k
     */
    public function __construct($documents, int $k = 2)
    {
        if ($k <= 0) {
            throw new \InvalidArgumentException('K must be greater than 0');
        }

        $this->k         = $k;
        $this->documents = collect($documents);

        // Get all unique words from all documents
        $this->uniqueWordLists = $this->makeUniqueWordLists();

        // Count the words and add index in each document
        $this->documents->each(function (Document $document, $index) {
            $document->countDocumentWords($this->uniqueWordLists);
            $document->setIndex($index);
        });

        $this->clusters = array_fill(0, $this->k, []);

        // Initialize the centroids with random documents
        $this->centroids = Centroid::createCentroid($this->documents, $k);
    }

    /**
     * Get all unique words from all documents
     *
     * @return \Illuminate\Support\Collection
     */
    public function makeUniqueWordLists()
    {
        // Get all words from all documents
        $words = $this->documents->map(fn(Document $document) => $document->getSentencesSplittedWords());

        // Flatten all the words
        $words = $words->flatten();

        // Remove duplicate words
        $words = $words->unique();
        $words = $words->values()->toArray();

        // Sort the words
        sort($words);

        return $words;
    }

    /**
     * Clusters the documents to its nearest centroid
     *
     * This method assigns each document to its nearest centroid
     * based on the Euclidean distance between the document and the centroid.
     *
     * @return void
     */
    public function clustering()
    {
        // Initialize the clusters with empty arrays
        $this->clusters = [];

        // Iterate over each document
        foreach ($this->documents as $document) {
            // Get the nearest centroid of the document
            $nearestCentroid = $document->nearestCentroid($this->centroids);

            // Add the document to its nearest centroid cluster
            $this->clusters[$nearestCentroid->index()][] = $document;
        }

        // Clean up the clusters by removing empty values
        $this->clusters = collect($this->clusters);
    }

    /**
     * Recalculate the centroid of the cluster
     *
     * This method recalculates the centroid of each cluster
     * by taking the average of all the documents in the cluster.
     *
     * @return void
     */
    public function recalculateCentroid()
    {
        // Iterate over each centroid
        foreach ($this->centroids as $centroid) {
            // Get the members of the centroid
            $members = $this->clusters[$centroid->index()];

            // Recalculate the centroid of the cluster
            $centroid->recalculateCentroid($members);
        }
    }

    /**
     * Get the documents that are being clustered
     *
     * This method returns the collection of documents that are being clustered.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Get the clusters of documents
     *
     * This method returns a collection of arrays, with each array
     * containing the documents in a cluster.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClusters()
    {
        return collect($this->clusters);
    }

    /**
     * Get the centroids of the clusters
     *
     * This method returns a collection of centroids
     * with each centroid cloned to avoid modifying the original centroids.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCentroids()
    {
        return $this->centroids->map(function (Centroid $centroid) {
            // Clone the centroid to avoid modifying the original centroid
            return clone $centroid;
        });
    }
}
