<?php foreach ($categoryVotes as $category => $votes): ?>
    <h2><?=$category?></h2>
    <table class="table">
        <?php foreach ($votes as $index => $vote): ?>
            <tr<?php if ($index < 3) echo ' class="success"' ?>>
                <td><a target="_blank" href="<?=$vote->url?>"><?=$vote->subject?></a></td>
                <td><?=$vote->member?></td>
                <td><?=$vote->date?></td>
                <td><strong><?=$vote->votes?></strong></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>
