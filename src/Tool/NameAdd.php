<?php

namespace App\Tool;

use App\Repository\CategoryCollection;
use App\Repository\TreeCollection;
use Exception;

class NameAdd
{
    private TreeCollection $tree;
    private CategoryCollection $category;
    private bool $skipOnNull = false;
    private int $maxDepth = 512;

    /**
     * @param int $maxDepth Maximum depth of tree to process
     */
    public function setMaxDepth(int $maxDepth): void
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @param bool $skipOnNull Set if skip when name of category was not found
     */
    public function setSkipOnNull(bool $skipOnNull): void
    {
        $this->skipOnNull = $skipOnNull;
    }

    /**
     * @param TreeCollection $tree Tree with categories
     */
    public function setTree(TreeCollection $tree): void
    {
        $this->tree = $tree;
    }

    /**
     * @param CategoryCollection $category List of categories with details
     */
    public function setCategory(CategoryCollection $category): void
    {
        $this->category = $category;
    }

    private string $localisation = 'pl_PL';

    /**
     * @param string $localisation Key of localisation in category list
     */
    public function setLocalisation(string $localisation): void
    {
        $this->localisation = $localisation;
    }

    /**
     * Reads categories list and tree from JSON
     *
     * @param string $categoryFile JSON file with category
     * @param int $maxDepth Maximum depth of JSON file
     */
    public function readFromFiles(string $categoryFile, string $treeFile, $maxDepth = 512): void
    {
        $this->category = new CategoryCollection();
        $this->category->readFromFile($categoryFile);
        $this->tree = new TreeCollection();
        $this->tree->readFromFile($treeFile, $maxDepth);
    }

    /**
     * Adding name field from category collection
     *
     * @param array $tree Reference to tree
     * @param int $maxDepth Current maximum depth of tree to process
     */
    protected function addNameToTree(array &$tree, int $maxDepth): void
    {
        if ($maxDepth > 0) {
            foreach ($tree as &$val) {
                /** @var array $val */
                if (array_key_exists('id', $val)) {
                    $name = $this->category->getCategoryNameById($val['id'], $this->localisation);
                    if (!$this->skipOnNull || !is_null($name)) {
                        $val['name'] = $name;
                    }
                    if (array_key_exists('children', $val) && !empty($val['children']))
                        $this->addNameToTree($val['children'], $maxDepth - 1);
                }
            }
        }
    }


    /**
     * Add name to category
     *
     * @return array Processed array
     */
    public function run(): array
    {
        $tree = $this->tree->asArray();
        $this->addNameToTree($tree, $this->maxDepth);
        return $tree;
    }

    /**
     * @throws Exception When it's unable to write to destination
     */
    public function runAndSaveTo(string $output, $pretty = false): void
    {
        $flag = $pretty ? JSON_PRETTY_PRINT : 0;
        $ret = file_put_contents($output, json_encode($this->run(),$flag));
        if($ret === false){
            throw new Exception('Unable to write output to file');
        }
    }

}