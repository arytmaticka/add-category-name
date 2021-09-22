<?php

namespace App\Tests;

use App\Repository\CategoryCollection;
use App\Repository\TreeCollection;
use App\Tool\NameAdd;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class NameAddTest extends TestCase
{
    private string $filesDir;
    private CategoryCollection $category;
    private TreeCollection $tree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesDir = dirname(__DIR__) . '/public/';
    }

    private function assertCategoryPL(int $id, string $assertName)
    {
        $this->assertEquals($assertName, $this->category->getCategoryNameById($id, 'pl_PL'));
    }

    /**
     * Reads categories list from JSON
     *
     * @param string $fileName JSON file with category
     */
    private function readCategory(string $fileName): void
    {
        $this->category = new CategoryCollection();
        $this->category->readFromFile($fileName);
    }

    /**
     * Read categories tree from JSON
     *
     * @param string $fileName JSON file with tree
     * @param int $maxDepth Maximum depth of JSON file
     */
    private function readTree(string $fileName, $maxDepth = 512): void
    {
        $this->tree = new TreeCollection();
        $this->tree->readFromFile($fileName, $maxDepth);
    }

    private function getCategoryFile(): string
    {
        return $this->filesDir . 'list.json';
    }

    private function getTreeFile(): string
    {
        return $this->filesDir . 'tree.json';
    }

    /**
     * Tests category collection
     */
    public function testCategories(): void
    {
        $this->readCategory($this->getCategoryFile());
        $this->assertCategoryPL(21, "Pielęgnacja ciała");
        $this->assertCategoryPL(11, "Buty");

        $this->assertNull($this->category->getCategoryNameById(21, 'en_US'));
    }

    public function testTree1(): void
    {
        $maxDepth = 1;
        $this->expectException(InvalidArgumentException::class);
        $this->readTree($this->getTreeFile(), $maxDepth);
    }

    public function testTree(): void
    {
        $this->readTree($this->getTreeFile());
        $this->assertCount(4, $this->tree->asArray()[2]['children']);
        $this->assertCount(5, $this->tree->asArray()[5]['children']);
    }

    private function getNameAddObject(): NameAdd
    {
        $srv = new NameAdd();
        $srv->readFromFiles($this->getCategoryFile(), $this->getTreeFile());
        return $srv;
    }

    private function assertProperCategoryNames(array $tree)
    {
        $this->assertEquals('Zdrowie i uroda', $tree[0]['name']);
        $this->assertEquals('Perfumy', $tree[0]['children'][0]['name']);

    }

    public function testAddingNameWithNull(): void
    {
        $srv = $this->getNameAddObject();
        $srv->setLocalisation('pl_PL');
        $srv->setMaxDepth(512);
        $srv->setSkipOnNull(false);
        $tree = $srv->run();
        $this->assertProperCategoryNames($tree);
        $this->assertNull($tree[0]['children'][2]['children'][0]['name']);

    }

    /**
     * Testing skipping empty category name
     */
    public function testAddingNameWithoutNull(): void
    {
        $srv = $this->getNameAddObject();
        $srv->setSkipOnNull(true);
        $tree = $srv->run();
        $this->assertProperCategoryNames($tree);
        $this->assertArrayNotHasKey('name', $tree[0]['children'][2]['children'][0]);
    }

    /**
     * Testing depth limit
     */
    public function testAddingDepth1(): void
    {
        $srv = $this->getNameAddObject();
        $srv->setSkipOnNull(true);
        $srv->setMaxDepth(1);
        $tree = $srv->run();
        $this->assertEquals('Zdrowie i uroda', $tree[0]['name']);
        $this->assertArrayNotHasKey('name', $tree[0]['children'][0]);
    }
}
