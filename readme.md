# HUisHU Options Framework

Bietet Funktionen für Optionen für HUisHU Themes und Plugins

## Verwendung:

Feld-Argumente einzusehen unter https://github.com/CMB2/CMB2/wiki/Field-Types

### Einfaches Feld auf der Haupt-Optionen-Seite einfügen:

```
add_filter('huishu_options_framework_main_page_fields','add_my_text_field');
function add_my_text_field($options){
    $options[] = array(
        'name'    => 'Test Text',
        'desc'    => 'field description (optional)',
        'default' => 'standard value (optional)',
        'id'      => 'wiki_test_text',
        'type'    => 'text',
    );
    return $options;
}
```

### Gruppenfeld auf der Haupt-Optionen-Seite einfügen:

```
add_filter('huishu_options_framework_main_page_fields','add_my_group_field');
function add_my_group_field($options){
    $options[] = array(
	    'id'          => 'wiki_test_repeat_group',
	    'type'        => 'group',
	    'description' => __( 'Generates reusable form entries', 'cmb2' ),
	    'options'     => array(
            'group_title'       => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
            'add_button'        => __( 'Add Another Entry', 'cmb2' ),
            'remove_button'     => __( 'Remove Entry', 'cmb2' ),
            'sortable'          => true,
            // 'closed'         => true, // true to have the groups closed by default
            // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
	    ),
        'groupfields' => array(
            array(
                'name'    => 'Test Text',
                'desc'    => 'field description (optional)',
                'default' => 'standard value (optional)',
                'id'      => 'wiki_test_text',
                'type'    => 'text',
            ),
        )
    );
    return $options;
}
```

### Zusätzliche Unter-Optionenseite mit Feldern anlegen:

```
add_filter('huishu_options_framework_options_pages','add_my_options_page');
function add_my_options_page($subpages){
    $subpages[] = array(
        'title' => 'Meine Sub-Options-Page', //Titel und links in der Menüleiste
        'options_key' => 'mein_options_key' //Options-Key, wird für den Abruf benötigt.
        'capability' => 'edit_other_pages' //default: manage_options
        'fields' => array(
            array(
                'name'    => 'Test Text',
                'desc'    => 'field description (optional)',
                'default' => 'standard value (optional)',
                'id'      => 'wiki_test_text',
                'type'    => 'text',
            ),
            array(
                'id'          => 'wiki_test_repeat_group',
                'type'        => 'group',
                'description' => __( 'Generates reusable form entries', 'cmb2' ),
                'options'     => array(
                    'group_title'       => __( 'Entry {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'        => __( 'Add Another Entry', 'cmb2' ),
                    'remove_button'     => __( 'Remove Entry', 'cmb2' ),
                    'sortable'          => true,
                ),
                'groupfields' => array(
                    array(
                        'name'    => 'Test Text',
                        'desc'    => 'field description (optional)',
                        'default' => 'standard value (optional)',
                        'id'      => 'wiki_test_text',
                        'type'    => 'text',
                    ),
                )
            )
        ),
    );
    return $subpages;
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