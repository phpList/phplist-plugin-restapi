<?php
namespace phpListRestapi;

class phpListRestapiDoc
{
    private $classes;

    public function __construct()
    {
    }

    public function addClass($classname)
    {
        $this->classes[] = "phpListRestapi\\$classname";
    }

    public function output()
    {
        $output = $this->header();

        foreach ($this->classes as $class) {
            $reflect = new \ReflectionClass($class);
            $methods = $reflect->getMethods();
            foreach ($methods as $method) {
                if (Common::method_allowed($reflect->getShortName(),$method->name)) {
                  $output .= '<section>';
                  $output .= '<div class="page-header">';
             #     $output .= '<h2>'.$reflect->getShortName().'</h2>';
                  $output .= '<h2>'.$method->name.'</h2>';
                  $output .= '</div>';
                  $output .= '<div class="row">';
                  $output .= '<div class="span12">';

                  $comment = $method->getDocComment();

                  $comment = str_replace('/**', '', $comment);
                  $comment = str_replace('*/', '', $comment);
                  $comment = str_replace('[*', '<span class="restapi-param param-required">', $comment);
                  $comment = str_replace('[', '<span class="restapi-param param-optional">', $comment);
                  $comment = str_replace(']', '</span>', $comment);
                  $comment = str_replace('{', '<span class="restapi-datatype">', $comment);
                  $comment = str_replace('}', '</span>', $comment);
                  $comment = str_replace('*', '', $comment);
                  //$comment = str_replace( '<br><br>', '', $comment );

                  $output .= trim($comment);

                  $output .= '</div>';
                  $output .= '</div>';
                  $output .= '<br/>';
                  $output .= '</section>';
               }
            }
        }

        $output .= $this->footer();

        return $output;
    }

    public function header()
    {
        return '
        
        <style type="text/css">
.param-required {
  background-color: #DD5600;
}
.param-optional {
    background-color: #669533;
}
.restapi-datatype {
    background-color: #999;
}

.restapi-param, .restapi-datatype {
    border-radius: 3px;
    display: inline-block;
    padding: 2px 4px;
    font-size: 11.844px;
    font-weight: bold;
    line-height: 14px;
    color: #FFF;
    vertical-align: baseline;
    white-space: nowrap;
    text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.25);
}

        </style>

                    <header class="jumbotron subhead" id="overview">
                        <div class="row">
                            <div class="xspan6">
                                <h1>API Plugin to phpList</h1>
                                <p class="lead">Documentation generated '. date('Y-m-d H:i:s').'
        </p>
                            </div>
                        </div>
                    </header>
                    <div class="row">
                        <div class="xspan12">
                            <div class="well">
                                The following methods is called by Body Param [cmd] to the plugin URL via request method POST.
                                <p>
                                    <span class="restapi-param param-required">Required body parameter</span><br/>
                                    <span class="restapi-param param-optional">Optional body parameter</span><br/>
                                    <span class="restapi-datatype">Datatype</span><br/>
                                </p>
                            </div>
                        </div>
                    </div>
        ';

    }

    public function footer()
    {
        return '
                  <footer id="footer">
                      <p class="pull-right"><a href="#">Back to top</a></p>
                  </footer>


        ';

    }
}
