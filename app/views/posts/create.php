<?php require APP_ROOT . '/views/layout/head.php'; ?>

<div class="navbar">
    <?php require APP_ROOT . '/views/layout/nav.php'; ?>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="container-register">
                <div class="wrapper-register">
                    <h1>New post</h1>
                    <form action="<?php echo URL_ROOT; ?>posts/create" method="post">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="title">
                                title
                                <?php echo $data['titleError']; ?>
                            </span>
                            <input type="text" class="form-control" name="title" placeholder="title" required>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                excerpt
                                <?php echo $data['excerptError']; ?>
                            </span>
                            <textarea class="form-control" id="excerpt" name="excerpt" placeholder="excerpt" required></textarea>
                        </div>

                        <div class="input-group mb-3 test">
                            <span class="input-group-text">
                                content
                                <?php echo $data['contentError']; ?>
                            </span>
                            <textarea class="form-control" id="test" name="content" placeholder="content" required></textarea>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">
                                category
                                <?php echo $data['categoryError']; ?>
                            </span>
                            <input type="text" class="form-control" name="category" placeholder="category" required>
                        </div>

                        <button id="submit" type="submit" value="Create">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace('excerpt');
    CKEDITOR.replace('content');
</script>

<div class="footer">
    <?php require APP_ROOT . '/views/layout/footer.php'; ?>
</div>