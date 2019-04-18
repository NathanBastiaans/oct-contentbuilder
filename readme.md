# OctoberCMS Content builder 
This is a simple proof of concept content builder plug-in. This plug-in works with the `RainLab Pages` plug-in and with
default models. 

## Why?
The default October CMS rich editor is cool, but an actual content builder is way cooler. 
Want to include images with a zoom effect? Easy. Want to do some cool custom quotes, video's or other stuff? Let's go! 
Want to change those cool custom video lay-out after a couple of weeks? Just change the partial and all blocks are 
changed.

## Getting started
There are a few steps you need to follow before everything is up and runnig. Just follow the simple steps below and 
everything should be fine. 
1. Modify the config to your liking
2. Add the contentRenderer component to the layout or page you intent to use the builder content.
3. Use the following twig tag to render the content: `{{ contentRenderer.renderContent(builder_content) | raw }}`
4. Enjoy your new content builder 

## Config 
You can edit the default builder.yaml file by changing the `default_builder_yaml` value in the config. 
In the `models` key you can assign the models you want to extend. Below is an example of the contents of `models`

```php
[
    'model_class' => \RainLab\Pages\Classes\Page::class,
    'builders'    => [
        'markup' => [
            'tab' => 'Content',
            'label' => 'Default content builder',
        ]
    ]
]
```

`model_class` is the Class of the model you want to extend (kind of obvious).

Within the `builders` array you can assign all fields that need the builders. The key is the field that gets replaced.
The `tab` is The tab in which the builder will be placed. This is done because the content can get quite long and might 
push other fields down to the bottom You can group multiple builders in the same tab if you want to. `label` is the label shown above the builder. 

There is also a way to change the 
builder config for just one field. Simply pass the `builder_config` key and the path to the relevant builder.yaml file 
as the value and behold another builder! 