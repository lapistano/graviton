<?php
namespace Graviton\GeneratorBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand as SymfonyGenerateBundleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Graviton\GeneratorBundle\Manipulator\BundleBundleManipulator;
use Graviton\GeneratorBundle\Generator\BundleGenerator;
use Graviton\GeneratorBundle\Definition\JsonDefinition;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\Output;
use Graviton\GeneratorBundle\Generator\DynamicBundleBundleGenerator;

/**
 * Here, we generate all "dynamic" Graviton bundles..
 * The workflow is as
 * follows:
 *
 * * Generate a BundleBundle, implementing the GravitonBundleInterface
 * * Generate our Bundles per JSON file
 * * Creating the necessary resources and files inside the newly created
 * bundles.
 * * All that in our own GravitonDyn namespace.
 *
 * Important: Why are we using shell_exec instead of just using the
 * internal API? Well, the main problem is, that we want to add resources (like
 * Documents) to our Bundles *directly* after generating them. Using the
 * internal API, we cannot add resources there using our tools as those Bundles
 * haven't been loaded yet through the AppKernel. Using shell_exec we can do
 * that.. This shouldn't be a dealbreaker as this task is only used on
 * deployment and/or development where a shell is accessible. It should be
 * executed in the same context as the previous generator tools, and also those
 * used the shell (backtick operator to get git name/email for example).
 *
 * @category GeneratorBundle
 * @package Graviton
 * @author Dario Nuevo <dario.nuevo@swisscom.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://swisscom.ch
 */
class GenerateDynamicBundleCommand extends ContainerAwareCommand
{
    
    private $bundleBundleNamespace;
    private $bundleBundleDir;
    private $bundleBundleClassname;
    private $bundleBundleClassfile;
    private $bundleBundleList = array();
    

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        
        $this->addOption('json', '', InputOption::VALUE_OPTIONAL, 
            'Path to the json definition.')
            ->addOption('bundleNameMask', '', InputOption::VALUE_OPTIONAL, 
            'Name mask', 'GravitonDyn/%sBundle')
            ->addOption('srcDir', '', InputOption::VALUE_OPTIONAL, 'Src Dir', 
            dirname(__FILE__) . '/../../../')
            ->addOption('bundleBundleName', '', InputOption::VALUE_OPTIONAL, 
            'Which BundleBundle to manipulate to add our stuff', 
            'GravitonDynBundleBundle')
            ->addOption('bundleFormat', '', InputOption::VALUE_OPTIONAL, 
            'Which format', 'xml')
            ->setName('graviton:generate:dynamicbundles')
            ->setDescription(
            'Generates all dynamic bundles in the GravitonDyn namespace. Either give a path
                    to a single JSON file or a directory path containing multipl files.');
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     *            input
     * @param OutputInterface $output
     *            output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundleNameMask = $input->getOption('bundleNameMask');
        
        /**
         * GENERATE THE BUNDLEBUNDLE
         */
        $namespace = sprintf($bundleNameMask, 'Bundle');
        $bundleName = str_replace('/', '', $namespace);
        $this->generateBundle($namespace, $bundleName, $input, $output, 'false');
        
        // bundlebundle stuff..
        $this->bundleBundleNamespace = $namespace;
        $this->bundleBundleDir = $input->getOption('srcDir').$namespace;
        $this->bundleBundleClassname = $bundleName;
        $this->bundleBundleClassfile = $this->bundleBundleDir.'/'
            .$this->bundleBundleClassname.'.php';
                
        /**
         * GENERATE THE BUNDLE
         */
        $jsonDef = new JsonDefinition($input->getOption('json'));
        
        $thisIdName = ucfirst(strtolower($jsonDef->getId()));
        $namespace = sprintf($bundleNameMask, $thisIdName);
        $bundleName = str_replace('/', '', $namespace);
        $this->generateBundle($namespace, $bundleName, $input, $output);
        
        $this->bundleBundleList[] = $namespace;
        
        // re-generate our bundlebundle..
        $this->generateBundleBundleClass();
        
        /**
         * GENERATE THE RESOURCE(S)
         */
        $arguments = array(
            'graviton:generate:resource',
            '--entity' => $bundleName . ':' . $thisIdName,
            '--format' => 'xml',
            '--fields' => $this->getFieldString($jsonDef),
            '--with-repository' => null
        );
        $this->executeCommand($arguments, $output);

        $output->writeln('Generated the bundle and the resource.');
    }

    private function generateBundle($namespace, $bundleName, 
        InputInterface $input, OutputInterface $output, $updateKernel = 'true')
    {
        
        // first, create the bundle
        $arguments = array(
            'graviton:generate:bundle',
            '--namespace' => $namespace,
            '--bundle-name' => $bundleName,
            '--dir' => $input->getOption('srcDir'),
            '--format' => $input->getOption('bundleFormat'),
            '--doUpdateKernel' => 'false',
            '--loaderBundleName' => $input->getOption('bundleBundleName'),
            '--structure' => null
        );
        
        return $this->executeCommand($arguments, $output);
    }

    private function executeCommand(array $args, OutputInterface $output)
    {
        $cmd = 'php app/console -n ';
        
        foreach ($args as $key => $val) {
            if (strlen($key) > 1)
                $cmd .= ' ' . $key;
            if (strlen($key) > 1 && ! is_null($val))
                $cmd .= '=';
            if (strlen($val) > 1)
                $cmd .= escapeshellarg($val);
        }
        
        $output->writeln(sprintf('Executing "%s"', $cmd));
        return shell_exec($cmd);
    }
    
    private function generateBundleBundleClass()
    {
        $dbbGenerator = new DynamicBundleBundleGenerator();
        $dbbGenerator->generate(
            $this->bundleBundleList,
            $this->bundleBundleNamespace,
            $this->bundleBundleClassname,
            $this->bundleBundleClassfile
        );        
    }

    /**
     * Returns the field string as described in the json file
     *
     * @param JsonDefinition $jsonDef
     *            The json def
     */
    private function getFieldString(JsonDefinition $jsonDef)
    {
        $ret = array();
        
        foreach ($jsonDef->getFields() as $field) {
            // don't add 'id' field it seems..
            if ($field->getName() != 'id') {
                $ret[] = $field->getName() . ':' . $field->getTypeDoctrine();
            }
        }
        
        return implode(' ', $ret);
    }
}