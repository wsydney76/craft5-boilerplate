# Faux Module

Enable autocomplete for Craft elements in Twig templates.

See comments in `FauxTwigExtension.php` for more info.

## Workaround

In case it doesn't work out of the box, run `craft faux/copy` after updating custom fields.

Notes: 

Autocompletion for custom fields works because `craft\base\Element` defines a mixin `craft\behaviors\CustomFieldBehavior`,
which lives in a file `CustomFieldBehavior_3@<randomstring` in  `storage/runtime/compiled_classes`.

For whatever reason, PhpStorm sometimes fails to pick up changes in this file, so the workaround copies it to a fixed location.
