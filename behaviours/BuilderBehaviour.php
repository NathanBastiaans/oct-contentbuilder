<?php

namespace Nathan\ContentBuilder\Behaviours;

use Cms\Classes\Content;
use Illuminate\Support\Facades\Event;
use Nathan\ContentBuilder\Exceptions\IncompatibleModelException;
use October\Rain\Database\Model;
use October\Rain\Support\Facades\Flash;
use RainLab\Pages\Classes\Page;
use October\Rain\Extension\ExtensionBase;

class BuilderBehaviour extends ExtensionBase
{
    /**
     * Holds the class name
     *
     * @var string
     */
    protected $model_class;

    /**
     * Holds the parent object
     *
     * @var Model|Page
     */
    protected $parent;

    /**
     * BuilderBehaviour constructor.
     *
     * @param $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;

        $this->model_class = get_class($parent);

        $this->bootContentBuilder();
    }

    /**
     * This method boots all content builder behaviour
     *
     * @return void
     */
    public function bootContentBuilder()
    {
        // Check if the extended class is actually a supported model
        $this->checkModelForContentBuilder();

        // Add a dynamic getter for the builders in case t does not exist
        if (!method_exists($this->parent, 'getBuilders')) {
            $this->parent->addDynamicMethod(
                'getBuilders',
                function () {
                    $data = collect(config('nathan.contentbuilder::models'))->filter(function($item) {
                        return array_get($item, 'model_class') == $this->model_class;
                    })->map(function($item) {
                        return array_get($item, 'builders');
                    })->toArray();

                    return array_first($data);
                }
            );
        }

        // If the the addJsonable method doesn't exist it's probably a RainLab page
        if (method_exists($this->parent, 'addJsonable')) {

            foreach ($this->parent->getBuilders() as $key => $builder) {
                $this->parent->addJsonable(
                    is_array($builder)
                        ? $key
                        : $builder
                );
            }

            // In case we added duplicates we make it unique again
            if (is_array($this->parent->jsonable)) {
                $this->parent->jsonable = array_unique($this->parent->jsonable);
            }
        }

        $this->parent::extend(
            function ($model) {
                if (get_class($model) == Page::class) {
                    // Extensions for RainLab pages
                    $this->extendRainLabPages($model);
                } else {
                    // Extensions for normal models
                    $this->extendModelSaving($model);
                    $this->extendModelFetching($model);
                    $this->extendModelDeleting($model);
                }

                Event::fire('nathan.contentbuilder.extendModel', [&$model]);
            }
        );
    }


    /**
     * Check if the behaviour is extending a compatible model
     *
     * @return void
     */
    protected function checkModelForContentBuilder()
    {
        // Perform a check to see if we actually have a compatible model
        try {
            if (!is_subclass_of($this->model_class, Model::class) && !is_subclass_of($this->model_class, Content::class)) {
                throw new IncompatibleModelException(
                    "The class '".$this->model_class."' using the PageBuilder"
                    . " behaviour is not a model"
                );
            }

        } catch (IncompatibleModelException $e) {
            trace_log($e->getMessage());
        }
    }

    /**
     * Extensions for the RainLab pages model
     *
     * @param $model
     */
    protected function extendRainLabPages($model)
    {

        // todo this has only been tested in the back-end front end testing needs to happen
        $model->bindEvent('model.afterFetch', function () use ($model) {


            foreach ($model->getBuilders() as $key => $builder) {
                if ($item = array_get($model->attributes['viewBag'], $key)) {
                    $model->attributes[$key] = json_encode($item, true);
                    unset($model->attributes['viewBag'][$key]);
                }
            }
        });
    }

    /**
     * Extend the fetch event of the model
     *
     * @param Model $model The model object
     *
     * @return void
     */
    protected function extendModelFetching($model)
    {
        $model->bindEvent(
            'model.afterFetch',
            function () use ($model) {
                // Don't extend in the back-end
                if (request()->is(config('cms.backendUri') . '*')) {
                    return;
                }

                Event::fire('nathan.contentbuilder.afterFetch', [&$model]);
            }
        );
    }

    /**
     * Extend the delete event of the model
     *
     * @param Model $model The model object
     *
     * @return void
     */
    protected function extendModelDeleting($model)
    {
        $model->bindEvent(
            'model.beforeDelete',
            function () use ($model) {
                // Here we can unset any non existing relationships
                Event::fire('nathan.contentbuilder.beforeDelete', [&$model]);
            }
        );
    }

    /**
     * Extend the save and validation events of the model
     *
     * @param Model $model The model object
     *
     * @return void
     */
    protected function extendModelSaving($model)
    {
        $model->bindEvent(
            'model.beforeSave',
            function () use ($model) {
                Event::fire('nathan.contentbuilder.beforeSave', [&$model]);
            }
        );

        $model->bindEvent(
            'model.beforeValidate',
            function () use ($model) {
                Event::fire('nathan.contentbuilder.beforeValidate', [&$model]);

                // The default validation rule.
                // We use sometimes because not all fields are always there
                $default_rule = "sometimes|required";

                // Translation base
                $base = 'nathan.contentbuilder::lang.validation.';

                foreach ($model->getBuilders() as $key => $builder) {
                    $field = $key;

                    // Add the validation rules for the field
                    $model->rules = array_merge(
                        $model->rules,
                        [
                            $field . '.*.editor_content' => $default_rule,
                            $field . '.*.image_path'     => $default_rule,
                            $field . '.*.image_alt'      => $default_rule,
                            $field . '.*.quote_content'  => $default_rule,
                            $field . '.*.video_type'     => $default_rule,
                            $field . '.*.video_url'      => $default_rule,
                        ]
                    );

                    $messages = [
                        $field.'.*.editor_content.required' => trans($base.'editor_content.required'),
                        $field.'.*.image_path.required'     => trans($base.'image_path.required'),
                        $field.'.*.image_alt.required'      => trans($base.'image_alt.required'),
                        $field.'.*.quote_content.required'  => trans($base.'quote_content.required'),
                        $field.'.*.video_type.required'     => trans($base.'video_type.required'),
                        $field.'.*.video_url.required'      => trans($base.'video_url.required'),
                    ];

                    if (!is_array($model->customMessages)) {
                        $model->customMessages = [];
                    }

                    $model->customMessages = array_merge(
                        $model->customMessages,
                        $messages
                    );
                }
            }
        );
    }
}