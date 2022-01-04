# HUisHU Options Framework

Bietet Funktionen für Optionen für HUisHU Themes und Plugins

## Verwendung:

Feld-Argumente einzusehen unter https://github.com/CMB2/CMB2/wiki/Field-Types

### Feld auf der Haupt-Optionen-Seite einfügen:

```
add_action('huishu_options_framework_do_additional_fields','add_my_text_field');
function add_my_text_field($cmb_options_main_page){
    $cmb_options_main_page->add_field(
        array(
            'name'    => 'Test Text',
            'desc'    => 'field description (optional)',
            'default' => 'standard value (optional)',
            'id'      => 'wiki_test_text',
            'type'    => 'text',
        )
    );
}
```

### Zusätzliche Unter-Optionenseite mit Feldern anlegen:

```
add_action('huishu_options_framework_do_additional_pages','add_my_options_page');
function add_my_options_page($main_options_key){
    $cmb_additional_options_page = new_cmb2_box( array(
        'id'           => 'my_custom_framework_subpage',
        'title'        => esc_html__( 'Meine Unterseite', 'myprefix' ),
        'object_types' => array( 'options-page' ),
        'option_key'      => $key, // The option key and admin menu page slug.
        // 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
        // 'menu_title'      => esc_html__( 'Options', 'myprefix' ), // Falls back to 'title' (above).
        'parent_slug'        => $main_options_key, // Make options page a submenu item of the themes menu.
            'capability'      => 'manage_options', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'myprefix' ), // The text for the options-page save button. Defaults to 'Save'.
    ) );
}
```

### Optionen der Haupt-Optionsseite abrufen:

```
hu_options_framework()->get_main_option('wiki_test_text','Standardwert');
```

### Optionen einer angelegten Options-Unterseite abrufen

```
hu_options_framework()->get_option('mein_options_key','wiki_test_text','Standardwert');
```