<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Filter;

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class Filter implements FilterInterface
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @var mixed|null
     *
     * @deprecated since sonata-project/admin-bundle 3.84, to be removed in 4.0.
     */
    protected $value;

    /**
     * @var array<string, mixed>
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function initialize($name, array $options = [])
    {
        $this->name = $name;
        $this->setOptions($options);
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getFormName()
    {
        /*
           Symfony default form class sadly can't handle
           form element with dots in its name (when data
           get bound, the default dataMapper is a PropertyPathMapper).
           So use this trick to avoid any issue.
        */

        return str_replace('.', '__', $this->name);
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getOption($name, $default = null)
    {
        if (\array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getFieldType()
    {
        return $this->getOption('field_type', TextType::class);
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getFieldOptions()
    {
        return $this->getOption('field_options', ['required' => false]);
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getFieldOption($name, $default = null)
    {
        if (isset($this->options['field_options'][$name]) && \is_array($this->options['field_options'])) {
            return $this->options['field_options'][$name];
        }

        return $default;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function setFieldOption($name, $value)
    {
        $this->options['field_options'][$name] = $value;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getLabel()
    {
        return $this->getOption('label');
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function setLabel($label)
    {
        $this->setOption('label', $label);
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getFieldName()
    {
        $fieldName = $this->getOption('field_name');

        if (!$fieldName) {
            throw new \RuntimeException(sprintf(
                'The option `field_name` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $fieldName;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getParentAssociationMappings()
    {
        return $this->getOption('parent_association_mappings', []);
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getFieldMapping()
    {
        $fieldMapping = $this->getOption('field_mapping');

        if (!$fieldMapping) {
            throw new \RuntimeException(sprintf(
                'The option `field_mapping` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $fieldMapping;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getAssociationMapping()
    {
        $associationMapping = $this->getOption('association_mapping');

        if (!$associationMapping) {
            throw new \RuntimeException(sprintf(
                'The option `association_mapping` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $associationMapping;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge(
            ['show_filter' => null, 'advanced_filter' => true],
            $this->getDefaultOptions(),
            $options
        );
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     *
     * @return array<string, mixed>
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @param mixed $value
     *
     * @deprecated since sonata-project/admin-bundle 3.84, to be removed in 4.0.
     */
    public function setValue($value)
    {
        @trigger_error(sprintf(
            'Method %s() is deprecated since sonata-project/admin-bundle 3.84 and will be removed in version 4.0.',
            __METHOD__,
        ), \E_USER_DEPRECATED);

        $this->value = $value;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @return mixed
     *
     * @deprecated since sonata-project/admin-bundle 3.84, to be removed in 4.0.
     */
    public function getValue()
    {
        @trigger_error(sprintf(
            'Method %s() is deprecated since sonata-project/admin-bundle 3.84 and will be removed in version 4.0.',
            __METHOD__,
        ), \E_USER_DEPRECATED);

        return $this->value;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function isActive()
    {
        $values = $this->value;

        // NEXT_MAJOR: Change for `return $this->active;`
        return $this->active
            || isset($values['value']) && false !== $values['value'] && '' !== $values['value'];
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    public function getTranslationDomain()
    {
        return $this->getOption('translation_domain');
    }

    /**
     * @final since sonata-project/admin-bundle 3.x.
     */
    protected function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
