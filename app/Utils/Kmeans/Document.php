<?php

namespace App\Utils\Kmeans;


class Document
{
    /**
     * @var string
     */
    protected $sentences;

    /**
     * @var array
     */
    protected $words;

    /**
     * @var array
     */
    private $uniqueWords;

    /**
     * @var int
     */
    private $index;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * Custom weights for each word
     *
     * @var array
     */
    private $customWeights = [];

    /**
     * Construct a new document instance from the given sentences
     *
     * @param string $sentences The sentences to construct the document from
     */
    public function __construct(string $sentences, $model = null)
    {
        $this->sentences = $sentences;
        $this->model     = $model;
    }

    /**
     * Count the words in the document and return the array of words and its occurrence
     *
     * @param array $uniqueWords
     * @return void
     */
    public function countDocumentWords(array $uniqueWords)
    {
        $this->uniqueWords = $uniqueWords;

        // Split the sentences into words
        $words = explode(' ', trim(strtolower($this->sanitizeSentences())));

        // Fill the array with the count of each word
        $values = array_fill(0, count($uniqueWords), 0);
        $array = array_combine($uniqueWords, $values);

        // Count the occurrence of each word
        $array = array_merge($array, array_count_values($words));

        // Sort the array by its keys
        ksort($array);

        // Store the array of words and its occurrence in the document
        $this->words = $array;

        // set custom weights
        if (count($this->customWeights) > 0) {

            foreach ($words as $word) {
                if (array_key_exists($word, $this->customWeights)) {
                    $this->words[$word] = $this->customWeights[$word];
                }
            }
        }
    }

    /**
     * Set the custom weights for the document
     *
     * This method sets the custom weights for the document. The custom weights
     * are used to calculate the value of each word in the document.
     *
     * @param array $weights The custom weights for each word in the document
     * @return void
     */
    public function setCustomWeights($weights)
    {
        $this->customWeights = $weights;
    }

    /**
     * Get the unique words from the document
     *
     * @return array
     */
    public function getUniqueWords()
    {
        return $this->uniqueWords;
    }

    /**
     * Get the model associated with the document
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Calculate the Euclidean distance between the document and the centroid
     *
     * @param Document $document
     * @return float
     */
    public function distance(Document $document)
    {
        $distance = 0;

        // Iterate over each unique word in the document
        foreach ($this->uniqueWords as $uniqueWord) {
            // Get the values of the word from the centroid and the document
            $centroidValue = $document->getValue($uniqueWord);
            $documentValue = $this->getValue($uniqueWord);

            // Calculate the Euclidean distance for the word
            $distance += pow(($centroidValue - $documentValue), 2);
        }

        // Return the square root of the sum of the Euclidean distances
        return sqrt($distance);
    }

    /**
     * Get the nearest centroid of the document from the collection of centroids.
     *
     * @param \Illuminate\Support\Collection<Document> $centroids
     * @return Document
     */
    public function nearestCentroid($centroids)
    {
        $distances = $this->getDistances($centroids);

        // Sort the distances and get the index of the nearest centroid
        $index = $distances->sort()->keys()->first();

        // Return the nearest centroid
        return $centroids->get($index);
    }

    /**
     * Calculate the Euclidean distance of the document to each centroid
     * and return a collection of the distances.
     *
     * @param \Illuminate\Support\Collection<Document> $centroids
     * @return \Illuminate\Support\Collection
     */
    public function getDistances($centroids)
    {
        if ($centroids->isEmpty()) {
            throw new \InvalidArgumentException('Centroids cannot be empty');
        }

        // If there is only one centroid, return it
        if ($centroids->count() == 1) {
            return $centroids->first();
        }

        // Calculate the Euclidean distance of the document to each centroid
        $distances = collect();

        foreach ($centroids as $centroid) {
            $distances->push($this->distance($centroid));
        }

        return $distances;
    }

    /**
     * Get the index of the document
     *
     * @return int
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * Set the index of the document
     *
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * Get the value of the given word in the document
     *
     * @param string $word
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getValue($word)
    {
        // Check if the word exists in the document
        if (!array_key_exists($word, $this->words)) {
            throw new \InvalidArgumentException("The word [{$word}] does not exist in the document");
        }

        // Return the value of the word
        return $this->words[$word];
    }

    /**
     * Check if the given document is the same as the current document
     *
     * A document is considered the same if all its words and their values
     * are the same as the current document.
     *
     * @param Document $document
     * @return bool
     */
    public function is(Document $document)
    {
        // Iterate over each word and its value in the document
        foreach ($this->words as $word => $value) {
            // Check if the word exists in the given document
            if (!array_key_exists($word, $document->words)) {
                // If the word does not exist, return false
                return false;
            }

            // Check if the values of the word are the same in both documents
            if ($this->words[$word] != $document->getValue($word)) {
                // If the values are not the same, return false
                return false;
            }
        }

        // If all the words and values are the same, check if the sentences are the same
        // This is a final check to ensure that the documents are the same
        return $this->sentences == $document->sentences;
    }

    /**
     * Split the sentences into unique words and return the array of words
     *
     * @return array
     */
    public function getSentencesSplittedWords()
    {
        // Split the sentences into words
        $words = explode(' ', trim(strtolower($this->sanitizeSentences())));

        // Remove duplicate words
        return array_unique($words);
    }

    /**
     * Set the words and their counts in the document
     *
     * This method sets the words and their counts in the document
     * to the given words.
     *
     * @param array $words The words and their counts in the document
     *
     * @return void
     */
    public function setWords($words)
    {
        $this->words = $words;
    }

    /**
     * Sanitize the sentences by removing numbers and extra spaces
     *
     * This method removes numbers and extra spaces from the sentences
     * and returns the sanitized sentences.
     *
     * @return string
     */
    public function sanitizeSentences()
    {
        // Remove numbers from the sentences
        $words = preg_replace('/\d+/u', '', $this->sentences);

        // Remove extra spaces from the sentences
        $words = preg_replace('/\s+/', ' ', $words);

        // Remove the ".pdf" string from the sentences
        return preg_replace('/\.pdf/', ' ', $words);
    }
}
