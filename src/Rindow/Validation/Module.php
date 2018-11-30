<?php
namespace Rindow\Validation;

use Rindow\Validation\Core\Validator as ValidatorClass;

class Module
{
    public function getConfig()
    {
        return array(
            'annotation' => array(
                'aliases' => array(
                    'Interop\\Lenient\\Validation\\Annotation\\AssertFalse' =>
                        'Rindow\\Validation\\Constraints\\AssertFalse',
                    'Interop\\Lenient\\Validation\\Annotation\\AssertTrue' =>
                        'Rindow\\Validation\\Constraints\\AssertTrue',
                    'Interop\\Lenient\\Validation\\Annotation\\CList' =>
                        'Rindow\\Validation\\Constraints\\CList',
                    'Interop\\Lenient\\Validation\\Annotation\\Constraint' =>
                        'Rindow\\Validation\\Constraints\\Constraint',
                    'Interop\\Lenient\\Validation\\Annotation\\Date' =>
                        'Rindow\\Validation\\Constraints\\Date',
                    'Interop\\Lenient\\Validation\\Annotation\\DateTimeLocal' =>
                        'Rindow\\Validation\\Constraints\\DateTimeLocal',
                    'Interop\\Lenient\\Validation\\Annotation\\Digits' =>
                        'Rindow\\Validation\\Constraints\\Digits',
                    'Interop\\Lenient\\Validation\\Annotation\\Email' =>
                        'Rindow\\Validation\\Constraints\\Email',
                    'Interop\\Lenient\\Validation\\Annotation\\Future' =>
                        'Rindow\\Validation\\Constraints\\Future',
                    'Interop\\Lenient\\Validation\\Annotation\\GroupSequence' =>
                        'Rindow\\Validation\\Constraints\\GroupSequence',
                    'Interop\\Lenient\\Validation\\Annotation\\Length' =>
                        'Rindow\\Validation\\Constraints\\Length',
                    'Interop\\Lenient\\Validation\\Annotation\\Max' =>
                        'Rindow\\Validation\\Constraints\\Max',
                    'Interop\\Lenient\\Validation\\Annotation\\Min' =>
                        'Rindow\\Validation\\Constraints\\Min',
                    'Interop\\Lenient\\Validation\\Annotation\\NotBlank' =>
                        'Rindow\\Validation\\Constraints\\NotBlank',
                    'Interop\\Lenient\\Validation\\Annotation\\NotNull' =>
                        'Rindow\\Validation\\Constraints\\NotNull',
                    'Interop\\Lenient\\Validation\\Annotation\\Past' =>
                        'Rindow\\Validation\\Constraints\\Past',
                    'Interop\\Lenient\\Validation\\Annotation\\Pattern' =>
                        'Rindow\\Validation\\Constraints\\Pattern',
                    'Interop\\Lenient\\Validation\\Annotation\\Size' =>
                        'Rindow\\Validation\\Constraints\\Size',
                ),
            ),
            'container' => array(
                'components' => array(
                    'Rindow\\Validation\\DefaultValidator' => array(
                        'class' => 'Rindow\\Validation\\Core\\Validator',
                        'constructor_args' => array(
                            'translator' => array('ref' => 'I18nMessageTranslator'),
                            'constraintManager' => array('ref' => 'Rindow\\Validation\\DefaultConstraintContextManager'),
                        ),
                        'properties' => array(
                            'translatorTextDomain' => array('value' => ValidatorClass::TRANSLATOR_TEXT_DOMAIN),
                        ),
                    ),
                    'Rindow\\Validation\\DefaultConstraintContextManager' => array(
                        'class' => 'Rindow\\Validation\\Core\\ConstraintContextManager',
                        // it will be able to inject annotation reader here.
                        'constructor_args' => array(
                            'serviceLocator' => array('ref' => 'ServiceLocator'),
                            'annotationReader' => array('ref' => 'AnnotationReader'),
                        ),
                        'properties' => array(
                            'config' => array('config' => 'validator'),
                        ),
                    ),
                    'Rindow\\Validation\\Builder\\DefaultAnnotationConstraintContextBuilder' => array(
                        'class' => 'Rindow\\Validation\\Builder\\AnnotationConstraintContextBuilder'
                    ),
                    'Rindow\\Validation\\Builder\\DefaultArrayConstraintContextBuilder' => array(
                        'class' => 'Rindow\\Validation\\Builder\\ArrayConstraintContextBuilder'
                    ),
                    'Rindow\\Validation\\Builder\\DefaultYamlConstraintContextBuilder' => array(
                        'class' => 'Rindow\\Validation\\Builder\\YamlConstraintContextBuilder',
                        'properties' => array(
                            'yaml' => array('ref' => 'Rindow\\Module\\Yaml\\Yaml'),
                        ),
                    ),
                ),
            ),
            'validator' => array(
                'builder_aliases' => array(
                    'annotation' => 'Rindow\\Validation\\Builder\\DefaultAnnotationConstraintContextBuilder',
                    'array' => 'Rindow\\Validation\\Builder\\DefaultArrayConstraintContextBuilder',
                    'yaml' => 'Rindow\\Validation\\Builder\\DefaultYamlConstraintContextBuilder',
                ),
                'builders' => array(
                    'annotation' => array(),
                ),
            ),
            'translator' => array(
                'translation_file_patterns' => array(
                    __NAMESPACE__ => array(
                        'type'        => 'Gettext',
                        'base_dir'    => ValidatorClass::getTranslatorBasePath(),
                        'pattern'     => ValidatorClass::getTranslatorFilePattern(),
                        'text_domain' => ValidatorClass::getTranslatorTextDomain(),
                    ),
                ),
            ),
        );
    }
}
