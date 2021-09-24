<?php
namespace Adeliom\EasyEditorBundle\Blocks;

use Adeliom\EasyEditorBundle\Block\AbstractBlock;
use App\Entity\Article;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ListType extends AbstractBlock
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add("list", CollectionType::class, [
                "entry_type" => TextType::class,
                'allow_add' => true
            ])
        ;

        /*
         *
        ->add("list", EntityType::class, [
                "class" => Article::class,
                "attr" => [
                    'data-ea-widget' => 'ea-autocomplete',
                ],
                'multiple' => true
            ])
         */


        // IF Multiple
        /*$builder->addModelTransformer(new CallbackTransformer(
            function ($data) {
                if ($data && $data["list"]){
                    $data["list"] = array_map(function ($entry){ return $this->manager->getRepository($entry["class"])->find($entry["id"]); }, $data["list"]);
                }
                return $data;
            },
            function ($data) {
                if($data["list"] instanceof ArrayCollection){
                    $data["list"] = $data["list"]->toArray();
                }
                if ($data["list"]){
                    $data["list"] = array_map(function ($entry){ return ["class" => get_class($entry), "id" => $entry->getId()]; }, (array) $data["list"]);
                }
                return $data;
            }
        ));*/

        //$builder->addModelTransformer($this->getTransformer());
    }

    public function getTransformer(){
        return new CallbackTransformer(
            function ($data) {
                if ($data && $data["list"]){
                    $data["list"] = $this->manager->getRepository($data["list"]["class"])->find($data["list"]["id"]);;
                }
                return $data;
            },
            function ($data) {
                if ($data["list"]){
                    $data["list"] = ["class" => Article::class, "id" => $data["list"]->getId()];
                }
                return $data;
            }
        );
    }


    public function getName(): string
    {
        return 'List';
    }

    public function getIcon(): string
    {
        return '<span class="fas fa-photo-video"></span>';
    }

    public function getTemplate(): string
    {
        return "@EasyEditor/block/media.html.twig";
    }
}
