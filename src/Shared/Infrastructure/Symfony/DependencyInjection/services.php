<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\DependencyInjection;

use DOMDocument;
use Google\Cloud\Monitoring\V3\Gapic\MetricServiceGapicClient;
use Holded\Accounting\Application\AccountingAccount\AccountingAccountLocker;
use Holded\Accounting\Application\Sii\SendNotificationOnInstalledEventHandler;
use Holded\Accounting\Application\Sii\SendNotificationOnUninstalledEventHandler;
use Holded\Accounting\Application\Sii\Service\FindPurchaseLiquidationPeriods;
use Holded\Accounting\Domain\AccountingAccount\AccountingAccountCreator;
use Holded\Accounting\Domain\AccountingAccount\AccountingAccountFinder;
use Holded\Accounting\Domain\AccountingAccount\AccountingAccountLocker as AccountingAccountLockerInterface;
use Holded\Accounting\Domain\AccountingBooks\PdfReportRepository;
use Holded\Accounting\Domain\AnnualAccounts\AnnualAccountPdfExporter as DomainAnnualAccountPdfExporter;
use Holded\Accounting\Domain\AnnualAccounts\AnnualAccountRepository;
use Holded\Accounting\Domain\AnnualAccounts\BalanceSheetDataRepository;
use Holded\Accounting\Domain\AnnualAccounts\MemoPdfExporter as DomainRemoteMemoPdfExporter;
use Holded\Accounting\Domain\AnnualAccounts\MemoPdfRetriever;
use Holded\Accounting\Domain\AnnualAccounts\MemoTemplateRepository;
use Holded\Accounting\Domain\AnnualAccounts\ProfitAndLossDataRepository;
use Holded\Accounting\Domain\AnnualAccounts\TrialBalanceDataRepository;
use Holded\Accounting\Domain\AnnualAccounts\XbrlExporter as DomainXbrlExporter;
use Holded\Accounting\Domain\AnnualAccounts\XmlExporter as DomainXmlExporter;
use Holded\Accounting\Domain\AnnualAccounts\ZipExporter;
use Holded\Accounting\Domain\Asset\AccountViewRepository as DomainAssetAccountRepository;
use Holded\Accounting\Domain\Asset\AmortizationSystemRepository;
use Holded\Accounting\Domain\Asset\ExcelGuideImporterRenderer;
use Holded\Accounting\Domain\Asset\OutputRenderer;
use Holded\Accounting\Domain\Chart\ChartRepository;
use Holded\Accounting\Domain\DocumentFields\AccountingFieldsValidator;
use Holded\Accounting\Domain\DocumentFields\FieldsStructureRepository;
use Holded\Accounting\Domain\Entry\DailyEntriesToUpdateRepository;
use Holded\Accounting\Domain\Entry\EntryTransferor;
use Holded\Accounting\Domain\OutOfBalance\BalanceChecker as DomainBalanceChecker;
use Holded\Accounting\Domain\Preferences\CnaeRepository;
use Holded\Accounting\Domain\Reporting\BalanceReportBuilder as DomainBalanceReportBuilder;
use Holded\Accounting\Domain\Reporting\ProfitAndLossReportBuilder as DomainProfitAndLossReportBuilder;
use Holded\Accounting\Domain\Reporting\ReportSchemeRepository;
use Holded\Accounting\Domain\Scheme\SchemeRepository;
use Holded\Accounting\Domain\Sii\AccountRepository;
use Holded\Accounting\Domain\Sii\Provider;
use Holded\Accounting\Domain\Sii\RemittanceExporter;
use Holded\Accounting\Domain\Sii\RemittanceFactory;
use Holded\Accounting\Domain\Sii\SubmissionCounterRepository;
use Holded\Accounting\Domain\Sii\UserRepository;
use Holded\Accounting\Infrastructure\AnnualAccounts\RemotePdf\Default\RemoteAnnualAccountPdfExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\RemotePdf\Default\RemoteMemoPdfExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\RemotePdf\DelegatingAnnualAccountPdfExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\RemotePdf\DelegatingRemoteMemoPdfExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\RemotePdf\V2023\RemoteAnnualAccountPdfExporter as V2023RemoteAnnualAccountPdfExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\RemotePdf\V2023\RemoteMemoPdfExporter as V2023RemoteMemoPdfExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\Default\IdentificationXbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\Default\ProfitAndLossXbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\Default\XbrlBalanceExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\Default\XbrlExporter as DefaultXbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\DelegatingXbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\V2023\IdentificationXbrlExporter as V2023IdentificationXbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\V2023\ProfitAndLossXbrlExporter as V2023ProfitAndLossXbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\V2023\XbrlBalanceExporter as V2023XbrlBalanceExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XbrlExporter\V2023\XbrlExporter as V2023XbrlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XmlExporter\Default\XmlExporter as DefaultXmlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XmlExporter\DelegatingXmlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\XmlExporter\V2023\XmlExporter as V2023XmlExporter;
use Holded\Accounting\Infrastructure\AnnualAccounts\ZipExporter\ZipArchiveExporter;
use Holded\Accounting\Infrastructure\Asset\Renderer\DelegatingOutputRenderer;
use Holded\Accounting\Infrastructure\Asset\Renderer\Excel\ExcelOutputRenderer;
use Holded\Accounting\Infrastructure\Asset\Renderer\ExcelGuideImporter\ExcelRenderer;
use Holded\Accounting\Infrastructure\DocumentFields\DelegatingAccountingFieldsValidator;
use Holded\Accounting\Infrastructure\DocumentFields\Ticketbai\TicketbaiValidator;
use Holded\Accounting\Infrastructure\Migrator\FixSiiDocumentsMigrator;
use Holded\Accounting\Infrastructure\Migrator\SetDeductionDateToSiiDocsMigrator;
use Holded\Accounting\Infrastructure\Migrator\SetSiiSubmissionCountersMigrator;
use Holded\Accounting\Infrastructure\Monitoring\DailyEntriesMeasurer;
use Holded\Accounting\Infrastructure\Monitoring\EnumerateEntriesMeasurer;
use Holded\Accounting\Infrastructure\OutOfBalance\BalanceChecker;
use Holded\Accounting\Infrastructure\Persistence\Doctrine\AnnualAccounts\DoctrineAnnualAccountRepository;
use Holded\Accounting\Infrastructure\Persistence\Doctrine\Asset\DoctrineAccountViewRepository;
use Holded\Accounting\Infrastructure\Persistence\Doctrine\Entry\DoctrineDailyEntriesToUpdateRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\AnnualAccounts\FileSystemMemoTemplateRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Asset\FileSystemAmortizationSystemRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Chart\FileSystemChartRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\DocumentFields\FileSystemFieldsStructureRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Preferences\FileSystemCnaeRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Reporting\FileSystemReportSchemeBuilder;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Reporting\FileSystemReportSchemeRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Reporting\ReportSchemeBuilder;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Scheme\FileSystemSchemeRepository;
use Holded\Accounting\Infrastructure\Persistence\FileSystem\Sii\FileSystemExportingStructureRepository;
use Holded\Accounting\Infrastructure\Persistence\MongoDB\Sii\MongoDBDocumentRepository;
use Holded\Accounting\Infrastructure\Persistence\Query\Sii\CommandSubmissionCounterRepository;
use Holded\Accounting\Infrastructure\Persistence\Report\AccountingBooks\AnnualAccountPdfReportRepository;
use Holded\Accounting\Infrastructure\Persistence\Report\AccountingBooks\HttpLegacyPdfReportRepository;
use Holded\Accounting\Infrastructure\Persistence\Report\AccountingBooks\LegacyPdfReportParamsTranslator;
use Holded\Accounting\Infrastructure\Persistence\Report\AccountingBooks\PdfReportStrategyRepository;
use Holded\Accounting\Infrastructure\Persistence\Report\AnnualAccounts\ReportBalanceSheetDataRepository;
use Holded\Accounting\Infrastructure\Persistence\Report\AnnualAccounts\ReportProfitAndLossDataRepository;
use Holded\Accounting\Infrastructure\Persistence\Report\AnnualAccounts\ReportTrialBalanceDataRepository;
use Holded\Accounting\Infrastructure\Reporting\BalanceReportBuilder;
use Holded\Accounting\Infrastructure\Reporting\ProfitAndLossReportBuilder;
use Holded\Accounting\Infrastructure\Sii\DocumentsRemittanceExporters\ExportingStructureRepository;
use Holded\Accounting\Infrastructure\Sii\DocumentsRemittanceExporters\Marosavat\MarosavatRemittanceExporter;
use Holded\Accounting\Infrastructure\Sii\Provider\Marosavat\Credentials;
use Holded\Accounting\Infrastructure\Sii\Provider\Marosavat\HttpClient;
use Holded\Accounting\Infrastructure\Sii\Provider\Marosavat\Marosavat;
use Holded\Accounting\Infrastructure\Sii\Provider\Marosavat\ResponseBuilder;
use Holded\Accounting\Infrastructure\Sii\Provider\Marosavat\ResponseBuilderV1;
use Holded\Core\Messaging\CommandBus;
use Holded\Invoicing\Domain\Documents\DocumentRepository;
use Holded\Shared\Domain\Migration\MigrationExecutors;
use Holded\Shared\Infrastructure\Http\LegacyInternal\HttpClient as LegacyInternalHttpClient;
use Holded\Tax\Domain\Tax\TaxRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ZipArchive;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $services = $container->services();

    $services
        ->set(ChartRepository::class, FileSystemChartRepository::class)
        ->set(AmortizationSystemRepository::class, FileSystemAmortizationSystemRepository::class)
        ->set(DailyEntriesToUpdateRepository::class, DoctrineDailyEntriesToUpdateRepository::class);

    $services->set(CnaeRepository::class, FileSystemCnaeRepository::class)->autowire();

    $container->services()->set(ExportingStructureRepository::class, FileSystemExportingStructureRepository::class);

    $container->services()->set(Provider::class, Marosavat::class)->autowire();

    $services->set(AccountingAccountFinder::class)->autowire();
    $services->set(EntryTransferor::class)->autowire();
    $services->set(SchemeRepository::class, FileSystemSchemeRepository::class);
    $services->set(FieldsStructureRepository::class, FileSystemFieldsStructureRepository::class);
    $services->set(MetricServiceGapicClient::class, MetricServiceGapicClient::class);
    $services->set(RemittanceExporter::class, MarosavatRemittanceExporter::class)->autowire();
    $services->set(RemittanceFactory::class)->autowire();
    $services->set(FindPurchaseLiquidationPeriods::class)->autowire();

    $services->set(DailyEntriesMeasurer::class)
        ->autowire()
        ->tag('monitoring.measurer', ['metric' => 'daily_entries']);

    $services->set(EnumerateEntriesMeasurer::class)
        ->autowire()
        ->tag('monitoring.measurer', ['metric' => 'enumerate_entries']);

    $services
        ->set(SendNotificationOnInstalledEventHandler::class)
        ->args(
            [
                service(AccountRepository::class),
                service(UserRepository::class),
                service(CommandBus::class),
                '%env(string:SII_SENDER_EMAIL)%',
                '%env(string:SII_INTERNAL_EMAIL)%',
            ]
        );

    $services
        ->set(SendNotificationOnUninstalledEventHandler::class)
        ->args(
            [
                service(AccountRepository::class),
                service(UserRepository::class),
                service(CommandBus::class),
                '%env(string:SII_SENDER_EMAIL)%',
                '%env(string:SII_INTERNAL_EMAIL)%',
            ]
        );

    // Marosavat
    $parameters->set('env(MAROSAVAT_BASE_URL)', 'https://sii.marosavat.com');
    $parameters->set('env(MAROSAVAT_ENVIRONMENT)', 'TEST');

    $services->set(HttpClient::class)->autowire();
    $services->set(ResponseBuilderV1::class)->autowire();
    $services->set(ResponseBuilder::class, ResponseBuilderV1::class)->autowire();
    $services->set(Credentials::class)->args([
        '%env(string:MAROSAVAT_BASE_URL)%',
        '%env(string:MAROSAVAT_ENVIRONMENT)%',
    ]);

    // Accounting Fields Validator
    $services->set(TicketbaiValidator::class)->autoconfigure()->autowire();
    $services->set(AccountingFieldsValidator::class, DelegatingAccountingFieldsValidator::class)
        ->args(
            [
                [
                    TicketbaiValidator::NAME => service(TicketbaiValidator::class),
                ],
            ],
        )
    ;

    // Report Scheme
    $services->set(ReportSchemeBuilder::class, FileSystemReportSchemeBuilder::class)->autowire();
    $services->set(ReportSchemeRepository::class, FileSystemReportSchemeRepository::class)->autowire();

    // Balance Checker
    $services->set(DomainBalanceChecker::class, BalanceChecker::class)->autowire();

    // Reporting Builders
    $services->set(DomainBalanceReportBuilder::class, BalanceReportBuilder::class)->autowire();
    $services->set(DomainProfitAndLossReportBuilder::class, ProfitAndLossReportBuilder::class)->autowire();

    $services->set(MongoDBDocumentRepository::class, MongoDBDocumentRepository::class)->autowire();

    $services->set(FixSiiDocumentsMigrator::class)
        ->tag(MigrationExecutors::TAG_REGISTER)
        ->args([
            service(MongoDBDocumentRepository::class),
            service(\Holded\Accounting\Domain\Sii\DocumentRepository::class),
            service(DocumentRepository::class),
            service(TaxRepository::class),
            service(CommandBus::class),
        ])
        ->autowire();

    $services->set(AnnualAccountRepository::class, DoctrineAnnualAccountRepository::class);

    $services->set(BalanceSheetDataRepository::class, ReportBalanceSheetDataRepository::class)->autowire();
    $services->set(ProfitAndLossDataRepository::class, ReportProfitAndLossDataRepository::class)->autowire();
    $services->set(TrialBalanceDataRepository::class, ReportTrialBalanceDataRepository::class)->autowire();
    $services->set(MemoTemplateRepository::class, FileSystemMemoTemplateRepository::class)->autowire();

    // RemoteMemoPdfExporter and RemoteAnnualAccountPdfExporter services
    $services->set(RemoteMemoPdfExporter::class)->autowire();
    $services->set(V2023RemoteMemoPdfExporter::class)->autowire();
    $services->set(RemoteAnnualAccountPdfExporter::class)->autowire();
    $services->set(V2023RemoteAnnualAccountPdfExporter::class)->autowire();

    $services->set(DomainRemoteMemoPdfExporter::class, DelegatingRemoteMemoPdfExporter::class)->autowire()->args([
        service(V2023RemoteMemoPdfExporter::class),
        service(RemoteMemoPdfExporter::class),
    ]);

    $services->set(DomainAnnualAccountPdfExporter::class, DelegatingAnnualAccountPdfExporter::class)->autowire()->args([
        service(V2023RemoteAnnualAccountPdfExporter::class),
        service(RemoteAnnualAccountPdfExporter::class),
    ]);

    $zipArchiveService = sprintf('zip_archive_%s', ZipArchiveExporter::class);
    $services->set($zipArchiveService, ZipArchive::class)->autowire();
    $services->set(ZipExporter::class, ZipArchiveExporter::class)->args(
        [
            service($zipArchiveService),
        ]
    )->autowire();

    // XmlExporter services
    $domDocumentForXmlService = sprintf('dom_document_%s', DefaultXmlExporter::class);
    $services->set($domDocumentForXmlService, DOMDocument::class)->args(['1.0', 'utf-8']);

    $services->set(DefaultXmlExporter::class)->autowire()->args([service($domDocumentForXmlService)]);
    $services->set(V2023XmlExporter::class)->autowire()->args([service($domDocumentForXmlService)]);

    $services->set(DomainXmlExporter::class, DelegatingXmlExporter::class)->autowire()->args([
        service(V2023XmlExporter::class),
        service(DefaultXmlExporter::class),
    ]);

    // XbrlExporter services
    $services->set(IdentificationXbrlExporter::class)->autowire();
    $services->set(ProfitAndLossXbrlExporter::class)->autowire();
    $services->set(XbrlBalanceExporter::class)->autowire();

    $services->set(V2023IdentificationXbrlExporter::class)->autowire();
    $services->set(V2023ProfitAndLossXbrlExporter::class)->autowire();
    $services->set(V2023XbrlBalanceExporter::class)->autowire();

    $domDocumentForDefaultXbrlService = sprintf('dom_document_%s', DefaultXbrlExporter::class);
    $domDocumentForV2023XbrlService = sprintf('dom_document_%s', V2023XbrlExporter::class);
    $services->set($domDocumentForDefaultXbrlService, DOMDocument::class)->args(['1.0', 'utf-8']);
    $services->set($domDocumentForV2023XbrlService, DOMDocument::class)->args(['1.0', 'utf-8']);
    $services->set(DefaultXbrlExporter::class)->autowire()->args([service($domDocumentForDefaultXbrlService)]);
    $services->set(V2023XbrlExporter::class)->autowire()->args([service($domDocumentForV2023XbrlService)]);
    $services->set(DomainXbrlExporter::class, DelegatingXbrlExporter::class)->autowire()->args([
        service(V2023XbrlExporter::class),
        service(DefaultXbrlExporter::class),
    ]);

    $services->set(MemoPdfRetriever::class)->autowire();

    $services->set(AccountingAccountLockerInterface::class, AccountingAccountLocker::class)->autowire();
    $services->set(AccountingAccountCreator::class)->autowire();

    $services->set(OutputRenderer::class, DelegatingOutputRenderer::class)->autowire();
    $services->set(ExcelOutputRenderer::class)->autowire();
    $services->set(ExcelGuideImporterRenderer::class, ExcelRenderer::class)->autowire();
    $services->set(DomainAssetAccountRepository::class, DoctrineAccountViewRepository::class);

    $services->set(LegacyPdfReportParamsTranslator::class);
    $services->set(HttpLegacyPdfReportRepository::class)
        ->args([
            service(LegacyInternalHttpClient::class),
            service(LegacyPdfReportParamsTranslator::class),
        ])
        ->autowire();
    $services->set(AnnualAccountPdfReportRepository::class)->autowire();
    $services->set(PdfReportRepository::class, PdfReportStrategyRepository::class)->autowire();

    $services->set(SetDeductionDateToSiiDocsMigrator::class)
        ->tag(MigrationExecutors::TAG_REGISTER)
        ->autowire();

    $services->set(SetSiiSubmissionCountersMigrator::class)
        ->tag(MigrationExecutors::TAG_REGISTER)
        ->autowire();

    $services->set(SubmissionCounterRepository::class, CommandSubmissionCounterRepository::class)->autowire();
};
