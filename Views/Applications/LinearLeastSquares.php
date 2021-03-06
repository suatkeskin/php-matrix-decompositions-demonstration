<!DOCTYPE html>
<html>
    <head>
        <title><?php echo __('Matrix Decompositions - Applications'); ?></title>
        <script type="text/javascript" src="/<?php echo $app->config('base'); ?>/Assets/Js/jquery.min.js"></script>
        <script type="text/javascript" src="/<?php echo $app->config('base'); ?>/Assets/Js/bootstrap.min.js"></script>
        <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
        <script type="text/javascript" src="/<?php echo $app->config('base'); ?>/Assets/Js/prettify.js"></script>
        <script type="text/x-mathjax-config">
            MathJax.Hub.Config({
                tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                window.prettyPrint() && prettyPrint();
            });
        </script>
        <link rel="stylesheet" type="text/css" href="/<?php echo $app->config('base'); ?>/Assets/Css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="/<?php echo $app->config('base'); ?>/Assets/Css/prettify.css">
    </head>
    <body>
        <a href="https://github.com/davidstutz/matrix-decompositions"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>
        <div class="container">
            <div class="page-header">
                <h1><?php echo __('Applications'); ?> <span class="muted">//</span> <?php echo __('Linear Least Squares'); ?></h1>
            </div>
              
            <div class="row">
                <div class="span3">
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('matrix-decompositions'); ?>"><?php echo __('Matrix Decompositions'); ?></a></li>
                        <li>
                            <a href="/<?php echo $app->config('base') . $app->router()->urlFor('applications'); ?>"><?php echo __('Applications'); ?></a>
                            <ul class="nav nav-pills nav-stacked" style="margin-left: 20px;">
                                <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('applications/system-of-linear-equations'); ?>"><?php echo __('System of Linear Equations'); ?></a></li>
                                <li class="active"><a href="#"><?php echo __('Linear Least Squares'); ?></a></li>
                            </ul>
                        </li>
                        <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('credits'); ?>"><?php echo __('Credits'); ?></a></li>
                    </ul>
                </div>
                <div class="span9">
                    <p>
                        <?php echo __('Given a mathematical model and some data, linear least squares is a method to fit the model to the data. As concrete example consider the following basic curve fitting problem. Imagine some points within a plane. We want to find a line within this plane such that the sum over all distances between the line and each point is minimized - so the line which has the best "fit" to the given data points. In general the problem can be described as (approximately) solving an overdetermined system of linear equations.'); ?>
                    </p>
                    
                    <p><b><?php echo __('Remark.'); ?></b> <?php echo __('A system of linear equations is overdetermined if it has more equations than unknowns.'); ?></p>
                    
                    <p><b><?php echo __('Problem.'); ?></b> <?php echo __('Given $A \in \mathbb{R}^{m \times n}$ with $m \geq n$ and full rank and $b \in \mathbb{R}^m$ find $x \in \mathbb{R}^n$ such that $\|Ax - b\|_2 = min_{y \in \mathbb{R}^n} \|Ay - b\|_2$.'); ?></p>
                    
                    <p>
                        <?php echo __('Using the QR decomposition the problem can easily be solved including computing the error $\|Ax - b\|_2$ of the found solution $x$. First calculate a QR decomposition $A = QR$. Then:'); ?>
                    </p>
                    
                    <p>
                        $Q^TA = R = \left[\begin{array}{c} 
                                    \bar{R} \\
                                    \emptyset \\
                                  \end{array} \right]$
                        <?php echo __('with $\bar{R} \in \mathbb{R}^{n \times n}$ and upper triangular matrix and'); ?>
                        $Q^Tb = \left[\begin{array}{c} 
                                    \bar{b} \\
                                    e \\
                                  \end{array} \right]$
                        <?php echo __('with $\bar{b} \in \mathbb{R}^{n}$.'); ?>
                    </p>
                    
                    <p>
                        <?php echo __('$\bar{R}$ has full rank and the solution $x \in \mathbb{R}^{n}$ of $\bar{R}c = \bar{b}$ is the solution of the linear least squares problem. In addition $\|e\|_2$ is the error.'); ?>
                    </p>
                    
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li><a href="#example-linear-line-fitting" data-toggle="tab"><?php echo __('Linear Line Fitting'); ?></a></li>
                            <li <?php if (!isset($matrix)): ?>class="active"<?php endif; ?>><a href="#demo" data-toggle="tab"><?php echo __('Demo'); ?></a></li>
                            <?php if (isset($matrix)): ?>
                                <li class="active"><a href="#result" data-toggle="tab"><?php echo __('Result'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <div class="tab-content">
                            <div id="example-linear-line-fitting" class="tab-pane">
                                <p>
                                    <?php echo __('As example we consider the problem of linear line fitting, that is we want to fit a linear model to a set of data in two-dimensional space. We consider the following data given:'); ?>
                                </p>
                                
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>$x$</th>
                                            <th>$y = f(x)$</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>$2$</td>
                                            <td>$13$</td>
                                        </tr>
                                        <tr>
                                            <td>$3$</td>
                                            <td>$14$</td>
                                        </tr>
                                        <tr>
                                            <td>$4$</td>
                                            <td>$14$</td>
                                        </tr>
                                        <tr>
                                            <td>$5$</td>
                                            <td>$12$</td>
                                        </tr>
                                        <tr>
                                            <td>$6$</td>
                                            <td>$10$</td>
                                        </tr>
                                        <tr>
                                            <td>$8$</td>
                                            <td>$6$</td>
                                        </tr>
                                        <tr>
                                            <td>$9$</td>
                                            <td>$4$</td>
                                        </tr>
                                        <tr>
                                            <td>$10$</td>
                                            <td>$2$</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <p>
                                    <?php echo __('The linear model we want to fit takes the form $y = f(x) = mx + n$. Therefore, we have $2$ unknowns we want to determine: $m$ and $n$. Note that fitting the above data is equivalent to saying we want to minimize $mx + n - y$. First, we need to rewrite this problem to match the more general definition of above. Plugging in our data we get the following system of equations:'); ?>
                                </p>
                                
                                <p>
                                    $13 = m\dot 2 + n$
                                    <br>
                                    $14 = m\dot 3 + n$
                                    <br>
                                    $14 = m\dot 4 + n$
                                    <br>
                                    $12 = m\dot 5 + n$
                                    <br>
                                    $10 = m\dot 6 + n$
                                    <br>
                                    $6 = m\dot 8 + n$
                                    <br>
                                    $4 = m\dot 9 + n$
                                    <br>
                                    $2 = m\dot 10 + n$
                                </p>
                                
                                <p>
                                    <?php echo __('We can rewrite this as $\|Ax - b\|_2$ with the matrix $A \in \mathbb{R}^{8 \times 2}$ and the vector $b \in \mathbb{R}^8$ as follows:'); ?>
                                </p>
                                
                                <p>
                                    $ A = \left[\begin{array}{c} 
                                            2 & 1\\
                                            3 & 1\\
                                            4 & 1\\
                                            5 & 1\\
                                            6 & 1\\
                                            8 & 1\\
                                            9 & 1\\
                                            10 & 1
                                        \end{array} \right],
                                        b = \left[\begin{array}{c} 
                                            13\\
                                            14\\
                                            14\\
                                            12\\
                                            10\\
                                            6\\
                                            4\\
                                            2
                                        \end{array} \right],
                                </p>
                            </div>
                            <div class="tab-pane <?php if (!isset($matrix)): ?>active<?php endif; ?>" id="demo">
                                <form class="form-horizontal" method="POST" action="/<?php echo $app->config('base') . $app->router()->urlFor('applications/linear-least-squares/demo'); ?>">
                                    <div class="control-group">
                                        <label class="control-label"><?php echo __('Matrix/Vector'); ?></label>
                                        <div class="controls">
                                            <div class="input-append">
                                                <textarea name="matrix" rows="10" class="span4">
1 0 0
0 1 0
0 0 1
                                                </textarea>
                                                <textarea name="vector" rows="10" class="span2" style="margin-left:10px;">
1
1
1
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button class="btn btn-primary" type="submit"><?php echo __('Solve'); ?></button>
                                    </div>
                                </form>
                            </div>
                            <?php if (isset($matrix)): ?>
                                <div class="tab-pane active" id="result">
                                    <p><b><?php echo __('Given matrix.'); ?></b></p>
                                    
                                    <p>$A := $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $matrix)); ?> $\in \mathbb{R}^{<?php echo $matrix->rows(); ?> \times <?php echo $matrix->columns(); ?>}$</p>
                                    
                                    <p><b><?php echo __('Given vector.'); ?></b></p>
                                    
                                    <p>$b := $<?php echo $app->render('Utilities/Vector.php', array('vector' => $vector)); ?> $\in \mathbb{R}^{<?php echo $vector->size(); ?>}$</p>
                                    
                                    <p><b><?php echo __('Decomposition.'); ?></b></p>
                                    
                                    <p>
                                        $Q = $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $q)); ?> $\leadsto
                                        \left[\begin{array}{c} 
                                            \bar{b} \\
                                            e \\
                                        \end{array} \right] = Q^Tb = $ <?php echo $app->render('Utilities/Vector.php', array('vector' => $b)); ?>
                                    </p>
                                    
                                    <p>
                                        $\bar{R} = $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $r)); ?>
                                    </p>
                                    
                                    <p><b><?php echo __('Solution $x$.'); ?></b></p>
                                    
                                    <p>$x := $ <?php echo $app->render('Utilities/Vector.php', array('vector' => $x)); ?> $ = \bar{R}^{-1} \bar{b} \in \mathbb{R}^{<?php echo $x->size(); ?>}$</p>
                                    
                                    <p><b><?php echo __('Check.'); ?></b></p>
                                    
                                    <p>
                                        <?php $res = Vector::add(Matrix::operate($matrix, $x), $vector->multiplyBy(-1.)); ?>
                                        $Ax - b = $ <?php echo $app->render('Utilities/Vector.php', array('vector' => $res)); ?> with $\|Ax - b\|_2 = \|e\|_2 = <?php echo $error; ?>$
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <p>
                &copy; 2013 David Stutz - <a href="/<?php echo $app->config('base'); ?><?php echo $app->router()->urlFor('credits'); ?>"><?php echo __('Credits'); ?></a> - <a href="http://davidstutz.de/impressum-legal-notice/"><?php echo __('Impressum - Legal Notice'); ?></a>
            </p>
        </div>
    </body>
</html>