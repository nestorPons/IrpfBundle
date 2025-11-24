<?php

namespace KimaiPlugin\IrpfBundle\EventSubscriber;

use App\Event\InvoiceMetaDefinitionEvent;
use App\Event\InvoiceTemplateMetaDefinitionEvent;
use App\Event\InvoicePreRenderEvent;
use App\Entity\InvoiceMeta;
use App\Entity\InvoiceTemplateMeta;
use App\Repository\InvoiceRepository;
use KimaiPlugin\IrpfBundle\Calculator\IrpfCalculator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class IrpfSubscriber implements EventSubscriberInterface
{
    public function __construct(private InvoiceRepository $invoiceRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InvoiceMetaDefinitionEvent::class => ['onInvoiceMetaDefinition', 100],
            InvoiceTemplateMetaDefinitionEvent::class => ['onInvoiceTemplateMetaDefinition', 100],
            InvoicePreRenderEvent::class => ['onInvoicePreRender', 100],
        ];
    }

    public function onInvoiceMetaDefinition(InvoiceMetaDefinitionEvent $event): void
    {
        $invoice = $event->getEntity();

        $meta = $invoice->getMetaField('irpf_applied');
        if ($meta === null) {
            $meta = new InvoiceMeta();
            $meta->setName('irpf_applied');
            $meta->setValue('0');
            $invoice->setMetaField($meta);
        }
        $meta->setLabel('Apply IRPF');
        $meta->setType(CheckboxType::class);
        $meta->setIsVisible(true);
        $meta->setIsRequired(false);

        $meta = $invoice->getMetaField('irpf_rate');
        if ($meta === null) {
            $meta = new InvoiceMeta();
            $meta->setName('irpf_rate');
            $meta->setValue('0.15');
            $invoice->setMetaField($meta);
        }
        $meta->setLabel('IRPF Rate');
        $meta->setType(PercentType::class);
        $meta->setIsVisible(true);
        $meta->setIsRequired(false);
    }

    public function onInvoiceTemplateMetaDefinition(InvoiceTemplateMetaDefinitionEvent $event): void
    {
        $template = $event->getEntity();

        $meta = $template->getMetaField('irpf_applied');
        if ($meta === null) {
            $meta = new InvoiceTemplateMeta();
            $meta->setName('irpf_applied');
            $meta->setValue('0');
            $template->setMetaField($meta);
        }
        $meta->setLabel('Apply IRPF');
        $meta->setType(CheckboxType::class);
        $meta->setIsVisible(true);
        $meta->setIsRequired(false);

        $meta = $template->getMetaField('irpf_rate');
        if ($meta === null) {
            $meta = new InvoiceTemplateMeta();
            $meta->setName('irpf_rate');
            $meta->setValue('0.15');
            $template->setMetaField($meta);
        }
        $meta->setLabel('IRPF Rate');
        $meta->setType(PercentType::class);
        $meta->setIsVisible(true);
        $meta->setIsRequired(false);
    }

    public function onInvoicePreRender(InvoicePreRenderEvent $event): void
    {
        $model = $event->getModel();
        
        // Try to find the invoice entity
        $invoice = null;
        try {
            $invoiceNumber = $model->getInvoiceNumber();
            if ($invoiceNumber) {
                $invoice = $this->invoiceRepository->findOneBy(['invoiceNumber' => $invoiceNumber]);
            }
        } catch (\Exception $e) {
            // Ignore
        }

        $applied = false;
        $rate = 0.15;

        // Check Invoice settings first
        if ($invoice !== null) {
            $appliedMeta = $invoice->getMetaField('irpf_applied');
            $rateMeta = $invoice->getMetaField('irpf_rate');
            
            if ($appliedMeta && $appliedMeta->getValue()) {
                $applied = (bool)$appliedMeta->getValue();
                $rate = $rateMeta ? (float)$rateMeta->getValue() : 0.15;
            }
        }

        // If not applied on invoice (or invoice not found), check Template settings
        if (!$applied) {
            $template = $model->getTemplate();
            if ($template) {
                $appliedMeta = $template->getMetaField('irpf_applied');
                $rateMeta = $template->getMetaField('irpf_rate');

                if ($appliedMeta && $appliedMeta->getValue()) {
                    $applied = (bool)$appliedMeta->getValue();
                    $rate = $rateMeta ? (float)$rateMeta->getValue() : 0.15;
                }
            }
        }

        if ($applied) {
            $calculator = $model->getCalculator();
            $irpfCalculator = new IrpfCalculator($calculator, $applied, $rate);
            $model->setCalculator($irpfCalculator);
        }
    }
}
