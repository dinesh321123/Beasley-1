<div class="container">
    <div class="content-wrap">
        <div class="section-head-container">
            <h2 class="section-head">
                <span class="bigger">
                <?php
                    $primary_category = ee_get_primary_category(get_the_ID());

                    if (!empty($primary_category)) {
                        echo $primary_category->name;
                    } else {
                        $categories = get_the_category();
                        if (!empty($categories)) {
                            echo $categories[0]->name;
                        }
                    }
                ?>
                </span>
            </h2>
        </div>
    </div>
</div>