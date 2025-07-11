<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseSeeder extends Seeder
{
    /**
     * The model class to seed.
     *
     * @var string
     */
    protected $model;

    /**
     * The data to seed.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->validateModel();
        $this->validateData();

        $this->beforeSeeding();

        foreach ($this->getData() as $item) {
            $this->createModel($item);
        }

        $this->afterSeeding();
    }

    /**
     * Validate that the model property is set and is a valid model class.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateModel(): void
    {
        if (empty($this->model) || !is_string($this->model) || !class_exists($this->model)) {
            throw new \InvalidArgumentException('The $model property must be set to a valid model class.');
        }

        if (!is_subclass_of($this->model, Model::class)) {
            throw new \InvalidArgumentException('The $model property must be a subclass of Illuminate\Database\Eloquent\Model.');
        }
    }

    /**
     * Validate that the data property is set and is an array.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateData(): void
    {
        if (!is_array($this->data)) {
            throw new \InvalidArgumentException('The $data property must be an array.');
        }
    }

    /**
     * Get the data to seed.
     *
     * @return array
     */
    protected function getData(): array
    {
        return $this->data;
    }

    /**
     * Create a model instance with the given attributes.
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createModel(array $attributes)
    {
        return $this->model::create($attributes);
    }

    /**
     * Hook that runs before seeding.
     */
    protected function beforeSeeding(): void
    {
        // This method can be overridden by child classes
    }

    /**
     * Hook that runs after seeding.
     */
    protected function afterSeeding(): void
    {
        // This method can be overridden by child classes
    }
}
