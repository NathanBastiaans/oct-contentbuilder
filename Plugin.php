<?php namespace Nathan\ContentBuilder;

use Backend;
use Backend\Widgets\Form;
use Cms\Classes\Controller;
use Illuminate\Support\Facades\Event;
use Nathan\ContentBuilder\Behaviours\BuilderBehaviour;
use Nathan\ContentBuilder\Components\ContentRenderer;
use RainLab\Pages\Classes\Page;
use System\Classes\PluginBase;

/**
 * ContentBuilder Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'nathan.contentbuilder::lang.plugin.name',
            'description' => 'nathan.contentbuilder::lang.plugin.description',
            'author'      => 'Nathan Bastiaans',
            'icon'        => 'icon-pencil'
        ];
    }

    public function registerComponents()
    {
        return [
            ContentRenderer::class => 'contentRenderer',
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        $this->extendModelClasses();

        Event::listen(
            'cms.page.init',
            function (Controller $controller) {
                $controller->addComponent(
                    ContentRenderer::class,
                    'contentRenderer',
                    [],
                    true
                );
            }
        );

        Event::listen('pages.object.save', function ($controller, $object, $type) {
            if ($type != 'page') {
                return;
            }
        });

        Event::listen(
            'backend.form.extendFields',
            function ($widget) {
                $this->addBuilderToModel($widget);
            }
        );
    }

    /**
     * Extend the model classes
     *
     * @return void
     */
    protected function extendModelClasses()
    {
        if (!is_array(config('nathan.contentbuilder::models'))) {
            return;
        }

        foreach (config('nathan.contentbuilder::models') as $data) {
            // Little sanity check for config values
            if ((!$model_class = array_get($data, 'model_class')) || !$builders = array_get($data, 'builders')) {
                continue;
            }

            $model_class::extend(
                function ($model) use ($data) {
                    // Check to avoid double extensions which cause exceptions
                    if (!$model->isClassExtendedWith(BuilderBehaviour::class)) {
                        $model->extendClassWith(BuilderBehaviour::class);
                    }
                }
            );
        }
    }

    /**
     * Add the page builder to models that use the PageBuilder trait
     *
     * @param Form $widget the FormWidget element
     *
     * @return void
     */
    protected function addBuilderToModel($widget)
    {
        // Only for the models that use the PageBuilder behaviour
        if (!$widget->model->isClassExtendedWith(BuilderBehaviour::class) || $widget->isNested) {
            return;
        }

        foreach ($widget->model->getBuilders() as $key => $builder) {

            $field          = $key;
            $tab            = array_get($builder, 'tab');
            $label          = array_get($builder, 'label');
            $custom_builder = array_get($builder, 'builder_config', null);

            // Remove any existing content fields to avoid having to double fill your data
            $widget->removeField($field);

            if (get_class($widget->model) == Page::class) {
                $field = 'viewBag['.$field.']';
            }

            // Add the field with the correct parameters
            $widget->addTabFields(
                [
                    $field => [
                        'label' => $label,
                        'tab' => $tab,
                        'type' => 'repeater',
                        'prompt' => 'nathan.contentbuilder::lang.builder.misc.repeater_prompt',
                        'groups' => ($custom_builder
                            ? $custom_builder
                            : config('nathan.contentbuilder::default_builder_yaml')),
                    ],
                ]
            );

            // Get all the tab fields
            $fields = $widget->getTabs()->primary->fields;

            // Order the tabs to set the builder as first tab
            $widget->getTabs()->primary->fields = array_merge(
                array_flip([$tab]),
                $fields
            );

        }
    }
}