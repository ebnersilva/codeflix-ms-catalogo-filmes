<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \Ramsey\Uuid\Uuid;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();

        $categories = Category::all();

        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            $categoryKey
        );
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test1'
        ]);
        $category->refresh();
        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertTrue(Uuid::isValid($category->id));
        $this->assertEquals(36, strlen($category->id));


        $category = Category::create([
            'name' => 'test1',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test1',
            'description' => 'test_description'
        ]);

        $this->assertEquals('test_description', $category->description);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();

        $data = [
          'name' => 'test_name_updated',
          'description'  => 'test_description_updated',
          'is_active' => true
        ];
        $category->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete() {
        $category = Category::create([
            'name' => 'category_01'
        ]);

        $category->refresh();

        $this->assertCount(1, Category::all());

        $category->delete();

        $category->refresh();

        $this->assertCount(0, Category::all());
    }
}
