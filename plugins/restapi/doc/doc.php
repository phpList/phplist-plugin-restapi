<?php


class phpList_RESTAPI_Doc{

    private $classes;

    function __construct()
    {
    }

    function addClass( $classname ){

        $this->classes[] = $classname;

    }

    function output(){

        $this->header();

        foreach( $this->classes as $class ){

            $reflect = new ReflectionClass( $class );
            $methods = $reflect->getMethods();
            foreach( $methods as $method ){

                echo '<section>';
                echo '<div class="page-header">';
                echo '<h2>' . $method->name . '</h2>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="span12">';

                $comment = $method->getDocComment();

                $comment = str_replace( '/**', '', $comment );
                $comment = str_replace( '*/', '', $comment );
                $comment = str_replace( '[*', '<span class="label label-warning">', $comment );
                $comment = str_replace( '[', '<span class="label label-success">', $comment );
                $comment = str_replace( ']', '</span>', $comment );
                $comment = str_replace( '{', '<span class="badge">', $comment );
                $comment = str_replace( '}', '</span>', $comment );
                $comment = str_replace( '*', '', $comment );
                //$comment = str_replace( '<br><br>', '', $comment );

                echo trim($comment);

                echo '</div>';
                echo '</div>';
                echo '<br/>';
                echo '<section>';
            }

        }

        $this->footer();

        exit;

    }


    function header(){

        ?>

        <!DOCTYPE html>
        <html>
            <head>
                <title>API Plugin to phpList</title>
                <!-- Bootstrap -->
                <link href="http://netdna.bootstrapcdn.com/bootswatch/2.1.1/cerulean/bootstrap.min.css" rel="stylesheet" media="screen">
            </head>
            <body>
                <div class="container">

                    <p>&nbsp;</p>

                    <header class="jumbotron subhead" id="overview">
                        <div class="row">
                            <div class="span6">
                                <h1>API Plugin to phpList</h1>
                                <p class="lead">Documentation generated <?php echo date('Y-m-d H:i:s'); ?></p>
                            </div>
                        </div>
                    </header>
                    <div class="row">
                        <div class="span12">
                            <div class="well">
                                The following methods is called by Body Param [cmd] to the plugin URL via request method POST.
                                <p>
                                    <span class="label label-warning">Required body parameter</span><br/>
                                    <span class="label label-success">Optional body parameter</span><br/>
                                    <span class="badge">Datatype</span><br/>
                                </p>
                            </div>
                        </div>
                    </div>
        <?php

    }

    function footer(){

        ?>
                  <footer id="footer">
                      <p class="pull-right"><a href="#">Back to top</a></p>
                  </footer>
                </div>
            </body>
        </html>

        <?php

    }


}


?>