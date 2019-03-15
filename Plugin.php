<?php namespace Nathan\ContentBuilder;

use Backend;
use Backend\Widgets\Form;
use Illuminate\Support\Facades\Event;
use Nathan\ContentBuilder\Behaviours\BuilderBehaviour;
use October\Rain\Database\Model;
use October\Rain\Support\Facades\Flash;
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
            'name'        => 'ContentBuilder',
            'description' => 'No description provided yet...',
            'author'      => 'Nathan',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register markup tags
     *
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'contentBuilder' => function($content) {
                    $renderer = new \ContentRenderer();
                    return $renderer->renderContent($content);
                }
            ]
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

                    // Add the builders config to the model so it can be used later
                    $model->addDynamicProperty(
                        'builders',
                        array_get($data, 'builders')
                    );

                    // Check to avoid double extensions which cause exceptions
                    if(!$model->isClassExtendedWith(BuilderBehaviour::class)) {
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

            $builder = $widget->model->getBuilderValuesByKey($key);

            $tab            = array_get($builder, 'tab');
            $label          = array_get($builder, 'label');
            $field          = array_get($builder, 'field');
            $custom_builder = array_get($builder, 'builder_config', null);

            // Remove any existing content fields to avoid having to double fill your data
            $widget->removeField($field);

            // Add the field with the correct parameters
            $widget->addTabFields(
                [
                    $field => [
                        'label'  => $label,
                        'tab'    => $tab,
                        'type'   => 'repeater',
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