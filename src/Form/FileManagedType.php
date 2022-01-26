<?php

namespace App\Form;

use App\Entity\FileManaged;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileManagedType extends AbstractType
{

    /**@var ParameterBagInterface**/
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::SUBMIT,function (FormEvent $event){
            /**@var UploadedFile $file**/
            $file = $event->getData();
            $originalName = $file->getClientOriginalName();
            $fileName = pathinfo(htmlspecialchars($originalName), PATHINFO_FILENAME).'-'. $file->getFilename() . '.' . $file->getClientOriginalExtension();
            $uploadPath = $this->parameterBag->get('base_path');
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            $file->move($uploadPath,$fileName);

            $fileManaged = new FileManaged();
            $fileManaged->setOriginName($originalName);
            $fileManaged->setFileName($fileName);
            $fileManaged->setMimeType($mimeType);
            $fileManaged->setPath($uploadPath.'/'.$fileName);
            $fileManaged->setFileSize($fileSize);

            $event->setData($fileManaged);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    public function getParent()
    {
        return FileType::class;
    }
}

