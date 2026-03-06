<?php

namespace Adeliom\EasyEditorBundle\Tests\Block;

use Adeliom\EasyEditorBundle\Block\BlockCollection;
use Adeliom\EasyEditorBundle\Block\BlockInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyEditorBundle\Block\BlockCollection::class)]
final class BlockCollectionTest extends TestCase
{
    public function testCollectionSortsAndFiltersAllowedBlocks(): void
    {
        $first = $this->createFirstBlock();
        $second = $this->createSecondBlock();
        $collection = new BlockCollection([$first, $second]);
        $sortedBlocks = array_values($collection->getBlocks()->toArray());

        self::assertSame('Second', $sortedBlocks[0]->getName());
        self::assertSame('First', $sortedBlocks[1]->getName());
        self::assertCount(2, $collection->getAllowedBlocks(null));
        self::assertCount(1, $collection->getAllowedBlocks([get_class($first)]));
    }

    public function testCollectionCanFilterUnsupportedBlocksForEntityDto(): void
    {
        $supported = $this->createSupportedBlock();
        $unsupported = $this->createUnsupportedBlock();
        $collection = new BlockCollection([$supported, $unsupported]);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getIdentifierFieldNames')->willReturn(['id']);
        $entityDto = new EntityDto(\stdClass::class, $metadata, null, new \stdClass());

        $filtered = $collection->enabledSupportFilter($entityDto)->getBlocks()->toArray();

        self::assertCount(1, $filtered);
        self::assertSame('Supported', array_values($filtered)[0]->getName());
    }

    private function createFirstBlock(): BlockInterface
    {
        return new class () implements BlockInterface {
            public function getName(): string
            {
                return 'First';
            }

            public function getIcon(): string|array
            {
                return 'fa-test';
            }

            public function getPosition(): int
            {
                return 20;
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/test.html.twig';
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public static function configureAdminAssets(): array
            {
                return ['js' => [], 'css' => []];
            }

            public static function configureAdminFormTheme(): array
            {
                return [];
            }

            public static function indexableProperties(): array
            {
                return [];
            }

            public function supports(string $objectClass, ?object $instance = null): bool
            {
                return true;
            }
        };
    }

    private function createSecondBlock(): BlockInterface
    {
        return new class () implements BlockInterface {
            public function getName(): string
            {
                return 'Second';
            }

            public function getIcon(): string|array
            {
                return 'fa-test';
            }

            public function getPosition(): int
            {
                return 10;
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/test.html.twig';
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public static function configureAdminAssets(): array
            {
                return ['js' => [], 'css' => []];
            }

            public static function configureAdminFormTheme(): array
            {
                return [];
            }

            public static function indexableProperties(): array
            {
                return [];
            }

            public function supports(string $objectClass, ?object $instance = null): bool
            {
                return true;
            }
        };
    }

    private function createSupportedBlock(): BlockInterface
    {
        return new class () implements BlockInterface {
            public function getName(): string
            {
                return 'Supported';
            }

            public function getIcon(): string|array
            {
                return 'fa-test';
            }

            public function getPosition(): int
            {
                return 10;
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/test.html.twig';
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public static function configureAdminAssets(): array
            {
                return ['js' => [], 'css' => []];
            }

            public static function configureAdminFormTheme(): array
            {
                return [];
            }

            public static function indexableProperties(): array
            {
                return [];
            }

            public function supports(string $objectClass, ?object $instance = null): bool
            {
                return true;
            }
        };
    }

    private function createUnsupportedBlock(): BlockInterface
    {
        return new class () implements BlockInterface {
            public function getName(): string
            {
                return 'Unsupported';
            }

            public function getIcon(): string|array
            {
                return 'fa-test';
            }

            public function getPosition(): int
            {
                return 20;
            }

            public function getTemplate(): string
            {
                return '@EasyEditor/test.html.twig';
            }

            public static function configureAssets(): array
            {
                return ['js' => [], 'css' => [], 'webpack' => []];
            }

            public static function configureAdminAssets(): array
            {
                return ['js' => [], 'css' => []];
            }

            public static function configureAdminFormTheme(): array
            {
                return [];
            }

            public static function indexableProperties(): array
            {
                return [];
            }

            public function supports(string $objectClass, ?object $instance = null): bool
            {
                return false;
            }
        };
    }
}
