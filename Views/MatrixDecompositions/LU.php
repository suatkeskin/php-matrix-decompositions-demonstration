<!DOCTYPE html>
<html>
    <head>
        <title><?php echo __('Matrix Decompositions - LU Decomposition'); ?></title>
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
                <h1><?php echo __('Matrix Decompositions'); ?> <span class="muted">//</span> <?php echo __('LU'); ?></h1>
            </div>
            
            <div class="row">
                <div class="span3">
                    <ul class="nav nav-pills nav-stacked">
                        <li>
                            <a href="/<?php echo $app->config('base') . $app->router()->urlFor('matrix-decompositions'); ?>"><?php echo __('Matrix Decompositions'); ?></a>
                            <ul class="nav nav-pills nav-stacked" style="margin-left: 20px;">
                                <li class="active"><a href="#"><?php echo __('LU Decomposition'); ?></a></li>
                                <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('matrix-decompositions/cholesky'); ?>"><?php echo __('Cholesky Decomposition'); ?></a></li>
                                <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('matrix-decompositions/qr'); ?>"><?php echo __('QR Decomposition'); ?></a></li>
                            </ul>
                        </li>
                        <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('applications'); ?>"><?php echo __('Applications'); ?></a></li>
                        <li><a href="/<?php echo $app->config('base') . $app->router()->urlFor('credits'); ?>"><?php echo __('Credits'); ?></a></li>
                    </ul>
                </div>
                <div class="span9">
                    <p>
                        <?php echo __('Based on Gaussian elimination the LU decomposition of a regular matrix $A \in \mathbb{R}^{n \times n}$ is a factorization into a lower normed triangular matrix $L \in \mathbb{R}^{n \times n}$ and an upper triangular matrix $U \in \mathbb{R}^{n \times n}$: $PA = LU$ where $P$ is a permutation matrix'); ?>.
                    </p>
                    
                    <p>
                        <b><?php echo __('Remark.'); ?></b> <?php echo __('If $A$ is regular the following statements hold:'); ?>
                        <ul>
                            <li><?php echo __('$det(A) \neq 0$'); ?></li>
                            <li><?php echo __('The system $Ax = b$ has a single unique solution for each $b \in \mathbb{R}^n$.'); ?></li>
                            <li><?php echo __('The matrix $A$ has full rank: $rank(A) = n$.'); ?></li>
                        </ul>
                    </p>
                    
                    <p>
                        <b><?php echo __('Applications.'); ?></b>
                        <ul>
                            <li>
                                <?php echo __('The solution of $Ax = b$ is then reduced to solving a system with a lower triangular matrix and one with an upper triangular matrix:
                                 $Ax = b \Leftrightarrow PAx = Pb \Leftrightarrow LRx = Pb$. Set $Rx = y$ and the problem is reduced to solving $Ly = Pb$ and $Rx = y$.'); ?>
                            </li>
                            <li>
                                <?php echo __('Calculating the determinant of the matrix $A$: $det(A) = (-1)^{\sharp swapped rows} \cdot \prod _{i=1} ^{n} u_{i,i}$ where $u_{i,i}$ is the entry of $U$ in the $i$-th row and $i$-th column.'); ?>
                            </li>
                            <li><?php echo __('Inverting the matrix $A$ by solving $n$ systems of linear equations given by $Ax = e_i$ where $e_i$ is the $i$-th unit vector.'); ?></li>
                        </ul>
                    </p>
                    
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li><a href="#code" data-toggle="tab"><?php echo __('Code'); ?></a></li>
                            <li><a href="#algorithm" data-toggle="tab"><?php echo __('Algorithm'); ?></a></li>
                            <li <?php if (!isset($matrix)): ?>class="active"<?php endif; ?>><a href="#demo" data-toggle="tab"><?php echo __('Demo'); ?></a></li>
                            <?php if (isset($matrix)): ?>
                                <li class="active"><a href="#result" data-toggle="tab"><?php echo __('Result'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="code">
                                <pre class="prettyprint linenums">
/**
 * Calculate a LU decomposition.
 *
 * @author  David Stutz
 * @license http://www.gnu.org/licenses/gpl-3.0
 */
class LU {
    
    /**
     * @var matrix
     */
    protected $_matrix;
    
    /**
     * @var vector
     */
    protected $_permutation;
    
    /**
     * Constructor: Generate LU decomposition of the matrix.
     *
     * @param   matrix  matrix to get the lu decomposition of
     * @return  vector  permutation
     */
    public function __construct(&$matrix) {
        new Assertion($matrix instanceof Matrix, 'Given matrix not of class Matrix.');
        new Assertion($matrix->isSquare(), 'Matrix is not quadratic.');

        $this->_permutation = new Vector($matrix->rows());
        $this->_matrix = $matrix->copy();
        
        for ($j = 0; $j < $this->_matrix->rows(); $j++) {

            $pivot = $j;
            for ($i = $j + 1; $i < $this->_matrix->rows(); $i++) {
                if (abs($this->_matrix->get($i, $j)) > abs($this->_matrix->get($pivot, $j))) {
                    $pivot = $i;
                }
            }

            $this->_permutation->set($j, $pivot);

            $this->_matrix->swapRows($j, $pivot);
            
            for ($i = $j + 1; $i < $this->_matrix->columns(); $i++) {
                $this->_matrix->set($i, $j, $this->_matrix->get($i, $j) / $this->_matrix->get($j, $j));

                for ($k = $j + 1; $k < $this->_matrix->columns(); $k++) {
                    $this->_matrix->set($i, $k, $this->_matrix->get($i, $k) - $this->_matrix->get($i, $j) * $this->_matrix->get($j, $k));
                }
            }
        }
    }

    /**
     * Solve system of linear equation using a right hand vector, the lu decomposition and the permutation vector of the lu decomposition.
     *
     * @param   vector  right hand
     */
    public function solve($b) {
        new Assertion($this->_matrix->rows() == $b->size(), 'Right hand vector does not have correct size.');
        
        $x = $b->copy();
        
        for ($i = 0; $i < $x->size(); $i++) {
            $x->swapEntries($i, $this->_permutation->get($i));
        }

        // First solve L*y = b.
        for ($i = 0; $i < $this->_matrix->rows(); $i++) {
            for ($j = $i - 1; $j >= 0; $j--) {
                $x->set($i, $x->get($i) - $x->get($j) * $this->_matrix->get($i, $j));
            }
        }

        // Now solve R*x =y.
        for ($i = $this->_matrix->rows() - 1; $i >= 0; $i--) {
            for ($j = $this->_matrix->columns() - 1; $j > $i; $j--) {
                $x->set($i, $x->get($i) - $x->get($j) * $this->_matrix->get($i, $j));
            }
            $x->set($i, $x->get($i) / $this->_matrix->get($i, $i));
        }

        return $x;
    }

    /**
     * Calculate the determinant of the matrix with the given lu decomposition.
     *
     * @param   matrix  lu decomposition
     * @param   vector  permutation vector of the lu decomposition
     */
    public function getDeterminant() {

        // Calculate number of swapped rows.
        $swapped = 0;
        for ($i = 0; $i < $this->_permutation->size(); $i++) {
            if ($this->_permutation->get($i) != $i) {
                $swapped++;
            }
        }

        $determinant = pow(-1, $swapped);

        for ($i = 0; $i < $this->_matrix->rows(); $i++) {
            $determinant *= $this->_matrix->get($i, $i);
        }

        return $determinant;
    }
    
    /**
     * Get the L of the decomposition.
     * 
     * @return  matrix  L
     */
    public function getL() {
        $L = $this->_matrix->copy();
        
        for ($i = 0; $i < $L->rows(); $i++) {
            for ($j = $i; $j < $L->columns(); $j++) {
                if ($i == $j) {
                    $L->set($i, $j, 1);
                }
                else {
                    $L->set($i, $j, 0);
                }
            }
        }
        
        return $L;
    }
    
    /**
     * Get the U of the decomposition.
     * 
     * @return  matrix  U
     */
    public function getU() {
        $U = $this->_matrix->copy();
        
        for ($i = 0; $i < $U->rows(); $i++) {
            for ($j = 0; $j < $i; $j++) {
                $U->set($i, $j, 0);
            }
        }
        
        return $U;
    }
    
    /**
     * Gets the row permutation.
     * 
     * @return  vector  permutation
     */
    public function getPermutation() {
        return $this->_permutation->copy();
    }
}

                                </pre>
                            </div>
                            <div class="tab-pane" id="algorithm">
                                <p>
                                    <?php echo __('Let $A \in \mathbb{R}^{n \times n}$ be a square matrix with $det(A) \neq 0$ and $b \in \mathbb{R}^n$ a column vector. Let $a_{i,j}$ denote the entry of $A$ in the $i$-th row and the $j$-th column.'); ?>
                                </p>
                                
                                <p>
                                    <?php echo __('The best known method for solving the system of linear equations given by $Ax = b$ is gaussian elimination (also known as row reduction).'); ?>
                                    <?php echo __('Gaussian elimination uses a sequence of elementary row operations to transform the matrix $A$ into an upper triangular matrix.'); ?> <?php echo __('There are three types of elementary row operations:'); ?>
                                </p>
                                
                                <ul>
                                    <li><?php echo __('Swap the $i$-th and $j$-th row of $A$.'); ?></li>
                                    <li><?php echo __('For $i \neq j$ add a multiple of the $j$-th row to the $i$-th row.'); ?></li>
                                    <li><?php echo __('For $c \neq 0$: multiply the $i$-th row of $A$ with c.'); ?></li>
                                </ul>
                                
                                <p>
                                    <b><?php echo __('Algorithm'); ?></b> <?php echo __('(without pivoting)'); ?>.
                                    <ul style="list-style-type:none;">
                                        <li><?php echo __('For $j = 1,2, \ldots, n-1$ and if $a_{j,j} \neq 0$:'); ?>
                                            <ul style="list-style-type:none;">
                                                <li><?php echo __('For $i = j+1,j+2, \ldots, n$:'); ?>
                                                    <ul style="list-style-type:none;">
                                                        <li><?php echo __('Subtract the $j$-th row of $\left[\begin{array}{rr} A & b \end{array} \right]$ from the $i$-th row multiplied by $l_{i,j} = \frac{a_{i,j}}{a_{j,j}}$'); ?></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </p>
                                
                                <p>
                                    <?php echo __('If $a_{j,j} \neq 0$ holds in every step the result has the form $\left[\begin{array}{rr} U & b \end{array} \right]$ for an upper triangular matrix $U$. Then the algorithm computes a factorization $A = LU$ where $L$ is a lower triangular matrix defined by its entries $l_{i,j}$ with $l_{i,i} = 1$. The algorithm fails if $a_{j,j} = 0$ in one of the steps.'); ?>
                                </p>
                                
                                <p>
                                    <?php echo __('Let assume $a_{j,j} = 0$ in the $j$-th step of the algorithm. Because swapping two rows does not change the solution of the system if applied on the right hand vector b, too, the algorithm could just swap the $j$-th row with a row $k > j$ such that $a_{k,j} \neq 0$. It can be shown, that selecting the $k$-th row such that $|a_{k,j}| \geq |a_{i,j}|$ for all $j < i \leq n$ will also have positive influence on the stability of the algorithm.'); ?>
                                    <?php echo __('This mechanism is called partial pivoting.'); ?>
                                </p>
                                
                                <p>
                                    <b><?php echo __('Algorithm.'); ?></b>
                                    <ul style="list-style-type:none;">
                                        <li><?php echo __('For $j = 1,2, \ldots, n-1$ and if $a_{j,j} \neq 0$:'); ?>
                                            <ul style="list-style-type:none;">
                                                <li><?php echo __('Find $k \in \{j + 1, \ldots , n\}$ such that $|a_{k,j}| \geq |a_{i,j}|$ for all $i \in \{j + 1, \ldots , n\}\backslash\{k\}$'); ?></li>
                                                <li><?php echo __('Swap the $j$-th row with the $k$-th row (pivoting)'); ?></li>
                                                <li><?php echo __('For $i = j+1,j+2, \ldots, n$:'); ?>
                                                    <ul style="list-style-type:none;">
                                                        <li><?php echo __('Subtract the $j$-th row of $\left[\begin{array}{rr} A & b \end{array} \right]$ from the $i$-th row multiplied by $l_{i,j} = \frac{a_{i,j}}{a_{j,j}}$'); ?></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </p>
                                
                                <p>
                                    <?php echo __('$k$ needs to be saved in each step, so the generated row permutation can be applied on the right hand vector $b$, too. In addition the number of swapped rows is important for calculating the determinant of $A$.'); ?>
                                </p>
                                
                                <p>
                                    <?php echo __('Given the above algorithm the runtime analysis using the uniform cost model is really intuitive. For each column $j$ substracting the $j$-th row from $n-j$ rows costs $n-j$ divisions and $(n-j)^2$ additions and multiplications:'); ?>
                                </p>
                                
                                <p><b><?php echo __('Runtime.'); ?></b></p>
                                
                                <p>
                                    <?php echo __('$T(n) = \sum _{j = 1} ^{n-1} (n - j) + n (n - j)^2 \approx \sum _{j = 1} ^{n-1} (n-j)^2 = \sum _{j = 1} ^{n-1} (j)^2$'); ?>
                                </p>
                                
                                <p>
                                    <?php echo __('$= \frac{n (n - 1) (2n - 1)}{6} = \frac{1}{6} (2n^3 - 3n^2 + n) \in \mathcal{O}(\frac{1}{3}n^3)$'); ?>
                                </p>
                            </div>
                            <div class="tab-pane <?php if (!isset($matrix)): ?>active<?php endif; ?>" id="demo">
                                <form class="form-horizontal" method="POST" action="/<?php echo $app->config('base') . $app->router()->urlFor('matrix-decompositions/lu/demo'); ?>">
                                    <div class="control-group">
                                        <label class="control-label"><?php echo __('Matrix'); ?></label>
                                        <div class="controls">
                                            <textarea name="matrix" rows="10" class="span6">
4 4 2
4 2 4
2 4 4
                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button class="btn btn-primary" type="submit"><?php echo __('Calculate LU Decomposition'); ?></button>
                                    </div>
                                </form>
                            </div>
                            <?php if (isset($matrix)): ?>
                                <div class="tab-pane active" id="result">
                                    <p><b><?php echo __('Given matrix.'); ?></b></p>
                                    
                                    <p>
                                        $A = $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $matrix)); ?> $\in \mathbb{R}^{<?php echo $matrix->rows(); ?> \times <?php echo $matrix->columns(); ?>}$
                                    </p>
                                    
                                    <p><b><?php echo __('Algorithm.'); ?></b></p>
                                    
                                    <?php foreach ($trace as $i => $array): ?>
                                        <p>
                                            $\leadsto$ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $array['permutation'])); ?> <?php echo __('Step'); ?> $(<?php echo $i; ?>)$ <?php echo __('Permutation.'); ?>
                                        </p>
                                        <p>
                                            $\leadsto$ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $array['elimination'])); ?> <?php echo __('Step'); ?> $(<?php echo $i; ?>)$ <?php echo __('Elimination.'); ?>
                                        </p>
                                    <?php endforeach; ?>
                                    
                                    <p><b><?php echo __('Decomposition.'); ?></b></p>
                                    
                                    <p>
                                        $L = $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $l)); ?>
                                    </p>
                                    
                                    <p>
                                        $U = $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => $u)); ?>
                                    </p>
                                    
                                    <p><b><?php echo __('CHeck.'); ?></b></p>
                                    
                                    <p>
                                        $LU = $ <?php echo $app->render('Utilities/Matrix.php', array('matrix' => Matrix::multiply($l, $u))); ?>
                                    </p>
                                    
                                    <?php if (isset($determinant)): ?>
                                        <p><b><?php echo __('Determinant.'); ?></b></p>
                                        
                                        <?php $swapped = 0; ?>
                                        <?php for ($i = 0; $i < $permutation->size(); $i++): ?>
                                            <?php if ($permutation->get($i) != $i): ?>
                                                <?php $swapped++; ?>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        
                                        <p>
                                            $(-1)^{\sharp swapped rows} \cdot \prod _{i=1} ^{n} u_{i,i} = (-1)^<?php echo $swapped; ?> <?php for ($i = 0; $i < $matrix->rows(); $i++): ?> \cdot <?php echo $matrix->get($i, $i); ?><?php endfor; ?> = <?php echo $determinant; ?>$
                                        </p>
                                    <?php endif; ?>
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