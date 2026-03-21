<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ExerciseHtmlPurifier'.\DIRECTORY_SEPARATOR.'HtmlProfilesConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ExerciseHtmlPurifierConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $defaultCacheSerializerPath;
    private $defaultCacheSerializerPermissions;
    private $htmlProfiles;
    private $_usedProperties = [];
    private $_hasDeprecatedCalls = false;

    /**
     * @default '%kernel.cache_dir%/htmlpurifier'
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function defaultCacheSerializerPath($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['defaultCacheSerializerPath'] = true;
        $this->defaultCacheSerializerPath = $value;

        return $this;
    }

    /**
     * @default 493
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function defaultCacheSerializerPermissions($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['defaultCacheSerializerPermissions'] = true;
        $this->defaultCacheSerializerPermissions = $value;

        return $this;
    }

    /**
     * @deprecated since Symfony 7.4
     */
    public function htmlProfiles(string $name, array $value = []): \Symfony\Config\ExerciseHtmlPurifier\HtmlProfilesConfig
    {
        $this->_hasDeprecatedCalls = true;
        if (!isset($this->htmlProfiles[$name])) {
            $this->_usedProperties['htmlProfiles'] = true;
            $this->htmlProfiles[$name] = new \Symfony\Config\ExerciseHtmlPurifier\HtmlProfilesConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "htmlProfiles()" has already been initialized. You cannot pass values the second time you call htmlProfiles().');
        }

        return $this->htmlProfiles[$name];
    }

    public function getExtensionAlias(): string
    {
        return 'exercise_html_purifier';
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('default_cache_serializer_path', $config)) {
            $this->_usedProperties['defaultCacheSerializerPath'] = true;
            $this->defaultCacheSerializerPath = $config['default_cache_serializer_path'];
            unset($config['default_cache_serializer_path']);
        }

        if (array_key_exists('default_cache_serializer_permissions', $config)) {
            $this->_usedProperties['defaultCacheSerializerPermissions'] = true;
            $this->defaultCacheSerializerPermissions = $config['default_cache_serializer_permissions'];
            unset($config['default_cache_serializer_permissions']);
        }

        if (array_key_exists('html_profiles', $config)) {
            $this->_usedProperties['htmlProfiles'] = true;
            $this->htmlProfiles = array_map(fn ($v) => new \Symfony\Config\ExerciseHtmlPurifier\HtmlProfilesConfig($v), $config['html_profiles']);
            unset($config['html_profiles']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['defaultCacheSerializerPath'])) {
            $output['default_cache_serializer_path'] = $this->defaultCacheSerializerPath;
        }
        if (isset($this->_usedProperties['defaultCacheSerializerPermissions'])) {
            $output['default_cache_serializer_permissions'] = $this->defaultCacheSerializerPermissions;
        }
        if (isset($this->_usedProperties['htmlProfiles'])) {
            $output['html_profiles'] = array_map(fn ($v) => $v->toArray(), $this->htmlProfiles);
        }
        if ($this->_hasDeprecatedCalls) {
            trigger_deprecation('symfony/config', '7.4', 'Calling any fluent method on "%s" is deprecated; pass the configuration to the constructor instead.', $this::class);
        }

        return $output;
    }

}
