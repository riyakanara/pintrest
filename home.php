<?php
// home.php
include 'db.php';

// saved pins for current user
$savedPins = [];
if (isset($_SESSION['user_id'])) {
    // Cleaned this line to remove invisible character
    $s = $conn->prepare("SELECT pin_id FROM saves WHERE user_id=?");
    $s->bind_param("i", $_SESSION['user_id']);
    $s->execute();
    $res = $s->get_result();
    while ($row = $res->fetch_assoc()) $savedPins[(int)$row['pin_id']] = true;
    $s->close();
}

// search
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($searchTerm !== '') {
    $like = '%' . $searchTerm . '%';
    $ps = $conn->prepare("SELECT p.*, u.username, u.profile_pic
                            FROM pins p
                            LEFT JOIN users u ON p.user_id=u.id
                            WHERE p.title LIKE ? OR p.description LIKE ? OR p.category LIKE ?
                            ORDER BY p.created_at DESC");
    $ps->bind_param("sss", $like, $like, $like);
    $ps->execute();
    $pinsRes = $ps->get_result();
    $ps->close();
} else {
    $pinsRes = $conn->query("SELECT p.*, u.username, u.profile_pic
                              FROM pins p
                              LEFT JOIN users u ON p.user_id=u.id
                              ORDER BY p.created_at DESC");
}

include_once 'header2.php';
?>

<main class="main-content">
    <section class="pins">
        <?php if ($pinsRes && $pinsRes->num_rows): ?>
            <?php while ($pin = $pinsRes->fetch_assoc()): ?>
                <?php
                    $img = htmlspecialchars($pin['image_url'] ?? '');
                    $title = htmlspecialchars($pin['title'] ?? '');
                    $pinId = (int)$pin['id'];
                    $isSaved = isset($savedPins[$pinId]);
                ?>
                
                <a href="pin.php?id=<?php echo $pinId; ?>" class="pin-link">
                    <article class="pin">
                        <img src="<?php echo $img; ?>" alt="<?php echo $title; ?>">
                        <div class="pin-hover">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="post" action="save.php" class="save-form" onclick="event.stopPropagation();">
                                    <input type="hidden" name="id" value="<?php echo $pinId; ?>">
                                    <button class="btn-save" type="submit"><?php echo $isSaved ? 'Saved' : 'Save'; ?></button>
                                </form>
                            <?php else: ?>
                                <a class="btn-save" href="login.php" onclick="event.stopPropagation();">Save</a>
                            <?php endif; ?>
                            <div class="pin-footer" onclick="event.stopPropagation();">
                                <div class="dropdown">
                                    <a href="#" class="icon"><i class="fa-solid fa-share"></i></a>
                                    <div class="dropdown-menu">
                                        <a href="#">Share</a>
                                        <a href="#" onclick="navigator.clipboard.writeText(window.location.origin+'/pin.php?id=<?php echo $pinId; ?>');return false;">Copy link</a>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="#" class="icon"><i class="fa-solid fa-ellipsis"></i></a>
                                    <div class="dropdown-menu">
                                        <a href="#">Like</a>
                                        <a href="download.php?file=<?php echo urlencode($pin['image_url']); ?>">Download</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty">No pins found.</div>
        <?php endif; ?>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // --- Shared Header Buttons (Account/Profile) ---
        const profileRedirectBtn = document.getElementById('user-profile'); 
        const profileToggleBtn = document.getElementById('topProfilePic'); 
        const accountSlider = document.getElementById('account-slider'); 
        
        // --- Home Page Pin Card Dropdowns ---
        const pinCardIcons = document.querySelectorAll('.pin-footer .icon');


        const closeAllDropdowns = () => {
            if (accountSlider) accountSlider.classList.remove('open');
            document.querySelectorAll('.pin-footer .dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        };
        
        // A) Sidebar Profile (Down Left) -> REDIRECTS to profile.php
        if (profileRedirectBtn) {
            profileRedirectBtn.addEventListener('click', (e) => {
                e.preventDefault(); 
                window.location.href = 'profile.php'; 
            });
        }

        // B) Topbar Profile (Top Right) -> TOGGLES ACCOUNT SLIDER
        if (profileToggleBtn && accountSlider) {
            profileToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isCurrentlyOpen = accountSlider.classList.contains('open');
                
                closeAllDropdowns(); // Close everything else (including pin card menus)
                
                if (!isCurrentlyOpen) {
                    accountSlider.classList.add('open');
                }
            });
            
            accountSlider.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // C) Home Page Pin Card Dropdowns (Share/More)
        pinCardIcons.forEach(icon => {
            icon.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Find the associated dropdown menu (must be the next sibling element in HTML)
                const dropdownMenu = icon.nextElementSibling;
                
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    const isAlreadyOpen = dropdownMenu.classList.contains('show');
                    
                    closeAllDropdowns(); // Close everything else
                    
                    if (!isAlreadyOpen) {
                        dropdownMenu.classList.add('show');
                    }
                }
            });
        });
        
        // D) Close ALL when clicking anywhere else
        document.addEventListener('click', closeAllDropdowns);
    });
</script>

<?php include_once 'footer2.php'; ?>