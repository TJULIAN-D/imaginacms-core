<?php
$themeManager = app('Modules\Core\Foundation\Theme\ThemeManager');
$themes = $themeManager->allPublicThemes();//Get themes
$locales = config('asgard.core.available-locales');//Get locales

//Order options to locale
$optionsLocales = [];
foreach ($locales as $key => $locale) {
  array_push($optionsLocales, ['label' => $locale['name'], 'value' => $key]);
}

//Order themes options
$themesOptions = [];
foreach ($themes as $key => $theme) {
  array_push($themesOptions, ['label' => $theme->getName(), 'value' => $key]);
}

return [
  'site-name' => [
    'name' => 'core::site-name',
    'value' => null,
    'type' => 'input',
    'isTranslatable' => true,
    'props' => [
      'label' => 'core::settings.site-name'
    ],
  ],
  'site-name-mini' => [
    'name' => 'core::site-name-mini',
    'value' => null,
    'type' => 'input',
    'isTranslatable' => true,
    'props' => [
      'label' => 'core::settings.site-name-mini'
    ],
  ],
  'site-description' => [
    'name' => 'core::site-description',
    'value' => null,
    'type' => 'input',
    'isTranslatable' => true,
    'props' => [
      'label' => 'core::settings.site-description',
      'type' => 'textarea',
      'rows' => 3,
    ],
  ],
  'template' => [
    'name' => 'core::template',
    'value' => null,
    'type' => 'select',
    'props' => [
      'label' => 'core::settings.template',
      'options' => $themesOptions
    ],
  ],
  'analytics-script' => [
    'name' => 'core::analytics-script',
    'value' => null,
    'type' => 'input',
    'props' => [
      'label' => 'core::settings.analytics-script',
      'type' => 'textarea',
      'rows' => 3,
    ],
  ],
  'locales' => [
    'name' => 'core::locales',
    'value' => [],
    'type' => 'select',
    'props' => [
      'label' => 'core::settings.locales',
      'multiple' => true,
      'useChips' => true,
      'options' => $optionsLocales
    ],
  ],
];
