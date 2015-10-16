<table border="1">
    <?php
    foreach ($grid as $arr) {
        echo "<tr>";
        foreach ($arr as $a) {
            echo "<td> $a </td>";
        }
        echo "</tr>";
    }
    ?>
</table>