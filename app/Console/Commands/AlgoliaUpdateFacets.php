<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Support\Facades\DB;

class AlgoliaUpdateFacets extends Command
{
    protected $signature = 'algolia:update-facets';
    protected $description = 'Update Algolia attributesForFaceting based on dynamic facet attributes per category';

    public function handle()
    {
        // Step 1: Get all unique facet keys from your facet_attributes table
        $facetKeys = DB::table('facet_attributes') // Make sure this is your correct table name
            ->distinct()
            ->pluck('name') // Adjust column name if needed
            ->filter() // Remove nulls or empty strings
            ->unique()
            ->values()
            ->toArray();

        $this->info("✅ Found " . count($facetKeys) . " unique dynamic facet keys.");

        // Step 2: Define static searchable+filterable facets
        $staticFacets = [
            'searchable(color)',
            'searchable(size)',
            'searchable(brand)',
            'searchable(category)',
        ];

        // Step 3: Convert dynamic facet keys to "searchable(facets.X)" format
        $dynamicFacets = array_map(fn($key) => 'searchable(' . $key .')', $facetKeys);

        // Step 4: Merge all
        $allFacets = array_merge($staticFacets, $dynamicFacets);

        // Step 5: Push to Algolia
        $indexName = config('scout.prefix', '') . config('scout.algolia.index', 'products');
        $appId = config('scout.algolia.id');
        $adminKey = config('scout.algolia.secret');

        if (!$appId || !$adminKey) {
            $this->error("❌ Missing Algolia credentials in config.");
            return;
        }

        $client = SearchClient::create($appId, $adminKey);
        $index = $client->initIndex($indexName);

        $index->setSettings([   
            'attributesForFaceting' => $allFacets
        ]);

        $this->info("✅ Algolia attributesForFaceting updated successfully for index: $indexName.");
    }
}
