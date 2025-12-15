
<?php
// Initialize the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
 
require_once "../db.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../auth/login.php");
    exit;
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $content = trim($_POST["content"]);

    $sql = "INSERT INTO reviews (user_ID, content, created_at) VALUES (?, ?, ?)";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "iss", $param_user_ID, $param_content, $param_created_at);
        
        $param_content = $content; 
        $param_user_ID = $_SESSION["id"];
        $param_created_at = date("Y-m-d H:i:s");

        if(!mysqli_stmt_execute($stmt)){
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        } else {
            echo "Inserted successfully!";
        }
    }
    header("Location: main.php");
    exit;

}

    $user_reviews = [];
    $sql = "SELECT * FROM reviews WHERE user_ID = ? ORDER BY created_at ASC";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $user_reviews[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MusicJournal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="mujo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- Replace the Inter link with this -->
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>
    <!-- top green nav bar-->
     <header class="top-nav">
        <div class="nav-left">
            <span class="logo">Music Journal</span>
        </div>
        <form action="../auth/logout.php" method="post">
            <button type="submit" class="nav-icon" title="logout">Logout</button>
        </form>
     </header>

     <!-- sidebar nav -->
      <aside class="sidebar">
        <nav class="sidebar-nav">
            <a href="#" class="sidebar-link active">
                <i class="icon fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="icon fas fa-search"></i>
                <span>Search</span>
            </a>
            <a href="discovery.html" class="sidebar-link">
                <i class="icon fas fa-compass"></i>
                <span>Discover</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="icon fas fa-music"></i>
                <span>Your Library</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="icon fas fa-user-friends"></i>
                <span>Friends</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="icon fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="account.html" class="sidebar-link">
                <i class="icon fas fa-user"></i>
                <span>Profile</span>
            </a>
        </nav>
      </aside>

     <!-- main two-column -->
      <main class="layout">
        <!-- left column -->
        <section class="card new-review">
            <h2> Hey <?php echo htmlspecialchars($_SESSION["username"]); ?>, Write a New Review...</h2>

            <form method="post">
                <textarea
                    name="content"
                    class="field textarea"
                    placeholder="Suggest a new song..."
                    required
                ></textarea>

                <div class="post-row">
                    <div class="left-controls">
                        <label class="toggle-switch">
                            <input type="checkbox" id="privacyToggle">
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">Private</span>
                        </label>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <button type="submit" class="post-btn">Post</button>
                    </form>
                </div>
            </form>
        </section>
        
        
        <section class="card your-reviews">
            <h2>Your Reviews</h2>
            <?php if(!empty($user_reviews)): ?>
                <ul>
                <?php foreach($user_reviews as $review): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($review['created_at']); ?>:</strong>
                        <?php echo htmlspecialchars($review['content']); ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="placeholder-text">You haven't written any reviews yet.</p>
            <?php endif; ?>
        </section>



             <!-- Library for Music + Friend Activity -->
            <section class="card library">
                <h2>Your Library</h2>

                <div class="library-list">
                    <p class="placeholder-text">Your saved songs, albums and artists</p>
                    <ul>
                        <li>▶ Motion Sickness - Phoebe Bridbgers</li>
                        <li>▶ August - Taylor Swift</li>
                        <li>▶ Smooth Operator - Sade</li>
                        <li>▶ Pride - Kendrick Lamar</li>
                        <li>▶ Pink + White - Frank Ocean</li>
                        <li>▶ August - Taylor Swift</li>
                        <li>▶ Smooth Operator - Sade</li>
                        <li>▶ Pride - Kendrick Lamar</li>
                        <li>▶ Motion Sickness - Phoebe Bridbgers</li>
                        <li>▶ August - Taylor Swift</li>
                        <li>▶ Smooth Operator - Sade</li>
                        <li>▶ Pride - Kendrick Lamar</li>
                        <li>▶ Motion Sickness - Phoebe Bridbgers</li>
                        <li>▶ August - Taylor Swift</li>
                        <li>▶ Smooth Operator - Sade</li>
                        <li>▶ Pride - Kendrick Lamar</li>
                    </ul>
                </div>
            </section>
        </section>

        <!-- right column -->
        <section class="right-column">
            <section class="card friends-feed">
                <h2>Friends</h2>

                <!-- friend review 1 -->
                <article class="friend-review">
                    <header class="friend-header">
                        <div class="left-info">
                            <div class="avatar"></div>
                            <div class="user-meta">
                                <div class="username">@user1</div>
                                <div class="meta">Album • Artist </div>
                                <div class="album-line">Album Name</div>
                            </div>
                        </div>

                        <div class="right-meta">
                                <div class="time">2h ago</div>
                                <div class="visibility">Friends</div>
                        </div>
                    </header>

                    <p class="review-text">
                        This song has basically soundtracked my entire week. Perfect for late night walks.
                    </p>

                   <footer class="review-actions">
                        <button class="action-btn">
                            <i class="fa-solid fa-thumbs-up"></i> 24
                        </button>

                        <button class="action-btn">
                            <i class="fa-solid fa-comment"></i> 5
                        </button>

                        <span class="separator">•</span>

                        <span class="visibility-label">Friends</span>
                    </footer>
                </article>

                <!-- friend review 2 -->
                <article class="friend-review">
                    <header class="friend-header">
                        <div class="left-info">
                            <div class="avatar"></div>
                            <div class="user-meta">
                                <div class="username">@user1</div>
                                <div class="meta">Album • Artist </div>
                                <div class="album-line">Album Name</div>
                            </div>
                        </div>

                        <div class="right-meta">
                                <div class="time">Yesterday</div>
                                <div class="visibility">Public</div>
                        </div>
                    </header>

                    <p class="review-text">
                        Can't stop listening to this song.
                    </p>

                   <footer class="review-actions">
                        <button class="action-btn">
                            <i class="fa-solid fa-thumbs-up"></i> 24
                        </button>

                        <button class="action-btn">
                            <i class="fa-solid fa-comment"></i> 5
                        </button>

                        <span class="separator">•</span>

                        <span class="visibility-label">Public</span>
                    </footer>
                </article>

                <!-- friend review 3 -->
                <article class="friend-review">
                    <header class="friend-header">
                        <div class="left-info">
                            <div class="avatar"></div>
                            <div class="user-meta">
                                <div class="username">@user1</div>
                                <div class="meta">Album • Artist </div>
                                <div class="album-line">Album Name</div>
                            </div>
                        </div>

                        <div class="right-meta">
                                <div class="time">30 minutes ago</div>
                                <div class="visibility">Friends</div>
                        </div>
                    </header>

                    <p class="review-text">
                        This album takes me back to summer 2019. Pure nostalgia in every track.

                    </p>

                   <footer class="review-actions">
                        <button class="action-btn">
                            <i class="fa-solid fa-thumbs-up"></i> 24
                        </button>

                        <button class="action-btn">
                            <i class="fa-solid fa-comment"></i> 5
                        </button>

                        <span class="separator">•</span>

                        <span class="visibility-label">Friends</span>
                    </footer>
                </article>

                <!-- friend review 4 -->
                <article class="friend-review">
                    <header class="friend-header">
                        <div class="left-info">
                            <div class="avatar"></div>
                            <div class="user-meta">
                                <div class="username">@user1</div>
                                <div class="meta">Album • Artist </div>
                                <div class="album-line">Album Name</div>
                            </div>
                        </div>

                        <div class="right-meta">
                                <div class="time">2h ago</div>
                                <div class="visibility">Friends</div>
                        </div>
                    </header>

                    <p class="review-text">
                        Found my new favorite song.
                    </p>

                   <footer class="review-actions">
                        <button class="action-btn">
                            <i class="fa-solid fa-thumbs-up"></i> 24
                        </button>

                        <button class="action-btn">
                            <i class="fa-solid fa-comment"></i> 5
                        </button>

                        <span class="separator">•</span>

                        <span class="visibility-label">Friends</span>
                    </footer>
                </article>

                <!-- friend review 5 -->
                <article class="friend-review">
                    <header class="friend-header">
                        <div class="left-info">
                            <div class="avatar"></div>
                            <div class="user-meta">
                                <div class="username">@user1</div>
                                <div class="meta">Album • Artist </div>
                                <div class="album-line">Album Name</div>
                            </div>
                        </div>

                        <div class="right-meta">
                                <div class="time">2h ago</div>
                                <div class="visibility">Friends</div>
                        </div>
                    </header>

                    <p class="review-text">
                        On repeat all day!
                    </p>

                   <footer class="review-actions">
                        <button class="action-btn">
                            <i class="fa-solid fa-thumbs-up"></i> 24
                        </button>

                        <button class="action-btn">
                            <i class="fa-solid fa-comment"></i> 5
                        </button>

                        <span class="separator">•</span>

                        <span class="visibility-label">Friends</span>
                    </footer>
                </article>

            </section>
        </section>
    </main>
</body>
</html>

                   
