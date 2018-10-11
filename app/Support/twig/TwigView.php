<?php
namespace Support\Twig;

use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigView
{
    /**
     * @var Twig_Environment
     */
    private $twig;
    /**
     * @var Twig_Loader_Filesystem
     */
    private $loader;
    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $params;

    /**
     * @param string $template Имя шаблона
     * @param array $params Передаваемые параметры
     */

    public function __construct($template, $params)
    {
        $this->loader = new Twig_Loader_Filesystem(TEMPLATE_DIR);
        $this->twig = new Twig_Environment($this->loader, array(
//            'cache' => ROOT .'/resourses/cache',
            'auto_reload' => true
        ));
        $this->template = $template;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function render()
    {

        try {
            return $this->twig->render($this->template, $this->params);
        } catch (\Twig_Error_Loader $e) {
            var_dump($e->getMessage());
        } catch (\Twig_Error_Runtime $e) {
            var_dump($e->getMessage());
        } catch (\Twig_Error_Syntax $e) {
            var_dump($e->getMessage());
        }
    }
}