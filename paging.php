<?php

echo "<ul class='pagination'>";

// ссылка для первой страницы
if ($page > 1) {
    echo "<li><a href='{$page_url}' title='Переход к первой странице'>";
    echo "Первая";
    echo "</a></li>";
}

// вычисление кол-ва страниц
$total_pages = ceil($total_rows / $products_per_page);

// диапазон ссылок для отображения
$range = 2;

//числа для гига-алгоритма
$initial_num = $page - $range; //первоначальное
$limit_num = ($page + $range) + 1; //лимитное

// выводим ссылки на страницы, пока первоначальное меньше лимитного
for ($x = $initial_num; $x < $limit_num; $x++) {

    if (($x > 0) && ($x <= $total_pages)) { // Вводим доп. проверки, чтобы не получить страницу -1 и тд.

        // когда цикл доходит до текущей страницы - срабатывает это условие
        if ($x == $page) {
            echo "<li class='active'><a href='#'>$x <span class='sr-only'>(current)</span></a></li>"; //текущая стр.
        }

        // до и после работает else
        else {
            echo "<li><a href='{$page_url}page=$x'>$x</a></li>"; //последующие и предыдущие
        }
    }
}

// ссылка на последнюю страницу
if ($page < $total_pages) {
    echo "<li><a href='{$page_url}page={$total_pages}' title='Переход к последней странице'>";
    echo "Последняя";
    echo "</a></li>";
}

echo "</ul>";