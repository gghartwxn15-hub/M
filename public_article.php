<?php
require_once('config.php');
session_start();

// ต้องตั้งค่า byear เพราะ connect() ในคลาส connect ต้องใช้ตัวแปรนี้
if (!isset($_SESSION['byear'])) {
    $_SESSION['byear'] = ($GLOBALS['yswitch'] <> 0) ? date('Y') : "";
}

require_once('class.connect.php');
$conn = new connect();

// escape ค่าที่รับจากผู้ใช้ให้ปลอดภัยก่อนนำไปต่อ SQL
// (รองรับหลายรูปแบบของคลาส connect โดยไม่ทำให้พังถ้าไม่มีเมธอดนั้น)
function esc_sql($conn, $str) {
    if (method_exists($conn, 'escape')) {
        return $conn->escape($str);
    }
    if (method_exists($conn, 'real_escape_string')) {
        return $conn->real_escape_string($str);
    }
    return addslashes($str);
}

// แปลง URL (http/https) ที่อยู่ในข้อความ (ซึ่งผ่าน htmlspecialchars() มาแล้ว)
// ให้กลายเป็นลิงก์ที่กดได้ โดยไม่กระทบความปลอดภัยของ escape เดิม
function auto_link_escaped($escaped_text) {
    $pattern = '/(https?:\/\/[^\s<]+?)(?=[.,)]*(?:\s|<br|$))/i';
    return preg_replace_callback($pattern, function ($m) {
        $url = rtrim($m[1], '.,)'); // ตัดเครื่องหมายวรรคตอนท้าย URL ออก
        return "<a href='" . $url . "' target='_blank' rel='noopener noreferrer'>" . $url . "</a>";
    }, $escaped_text);
}

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'list';
$id   = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0; // cast int กัน SQL Injection

// คำค้นหา + ฟิลเตอร์โรค (ใช้ในหน้ารายการ)
$q       = isset($_GET['q']) ? trim($_GET['q']) : '';
$type_id = isset($_GET['type']) ? (int)$_GET['type'] : 0; // 0 = ทั้งหมด

// เลือกภาพประกอบสำรอง (เมื่อบทความยังไม่มีรูปอัปโหลด) ตามคำในชื่อโรค
function health_fallback_theme($disease_name) {
    $n = $disease_name;
    if (mb_strpos($n, 'ไต') !== false) return 'kidney';
    if (mb_strpos($n, 'เบาหวาน') !== false) return 'diabetes';
        return 'nutrition';
}

// วาดภาพประกอบ SVG ธีมสุขภาพ ใช้แทนรูปเมื่อไม่มีการอัปโหลดรูปภาพ
function render_health_svg($theme) {
    $gradients = [
        'kidney'    => ['#E7F3EC', '#CDE8DA'],
        'diabetes'  => ['#FCEFD9', '#F6DBAE'],
        
    ];
    [$c1, $c2] = $gradients[$theme] ?? $gradients['nutrition'];
    $gid = 'g_' . $theme . '_' . mt_rand(1000, 9999);
    ob_start();
    ?>
    <svg class="article-visual-svg" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="<?php echo $gid; ?>" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="<?php echo $c1; ?>"/>
                <stop offset="1" stop-color="<?php echo $c2; ?>"/>
            </linearGradient>
        </defs>
        <rect width="400" height="300" fill="url(#<?php echo $gid; ?>)"/>
        <?php if ($theme === 'kidney'): ?>
            <ellipse cx="200" cy="250" rx="130" ry="14" fill="#B9D9C6" opacity=".6"/>
            <path d="M110 165 a90 40 0 0 0 180 0 Z" fill="#FFFFFF" stroke="#0F5C42" stroke-width="4"/>
            <ellipse cx="200" cy="165" rx="90" ry="18" fill="#F3FAF6" stroke="#0F5C42" stroke-width="4"/>
            <ellipse cx="170" cy="163" rx="34" ry="10" fill="#FFF8E7"/>
            <circle cx="222" cy="160" r="10" fill="#EE8B60"/>
            <ellipse cx="200" cy="170" rx="12" ry="7" fill="#4C9A5F"/>
        <?php elseif ($theme === 'diabetes'): ?>
            <ellipse cx="200" cy="250" rx="130" ry="14" fill="#EAC489" opacity=".6"/>
            <rect x="155" y="110" width="60" height="90" rx="10" fill="#FFFFFF" stroke="#8A5A12" stroke-width="4"/>
            <rect x="164" y="122" width="42" height="28" rx="4" fill="#8A5A12"/>
            <circle cx="170" cy="168" r="5" fill="#F0C27B"/>
            <circle cx="188" cy="168" r="5" fill="#F0C27B"/>
            <circle cx="170" cy="182" r="5" fill="#F0C27B"/>
            <circle cx="188" cy="182" r="5" fill="#F0C27B"/>
            <path d="M260 120 c12 15 12 27 0 36 c-12 -9 -12 -21 0 -36Z" fill="#C0512F"/>
        <?php endif; ?>
    </svg>
    <?php
    return ob_get_clean();
    }
    ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>บทความ - Bon_Appetit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root{
            --ink:#12261F;
            --forest:#0F5C42;
            --forest-dark:#0B4632;
            --bg:#FBFAF6;
            --line:#E3EBE5;
        }
        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--bg);
            color: var(--ink);
            font-size: 18px;      /* larger base size for readability */
           line-height: 1.7;
        }		/* พื้นหลังบาร์ล้ำยุค ไล่สีจากโทนเข้มไปหาเขียวมรกตลึกๆ */
		.bg-cyber-health {
			background: linear-gradient(135deg, #0f172a 0%, #064e3b 100%) !important; 
			box-shadow: 0 4px 30px rgba(16, 185, 129, 0.15); /* เรืองแสงเบาๆ ใต้บาร์ */
			backdrop-filter: blur(10px); /* เอฟเฟกต์กระจกเบลอ */
			border-bottom: 1px solid rgba(52, 211, 153, 0.2);
		}

		/* เอฟเฟกต์ข้อความโลโก้แบบมีมิติ */
		.brand-glow {
			background: linear-gradient(to right, #34d399, #38bdf8); /* ไล่สีเขียวมิ้นต์ไปฟ้า */
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			font-weight: 800;
			letter-spacing: 1px;
		}

		/* ตัวหนังสือเมนูทั่วไป */
		.navbar-dark .navbar-nav .nav-link {
			color: #94a3b8; /* สีเทาสว่าง ดูสบายตา */				font-weight: 500;
			letter-spacing: 0.5px;
			transition: all 0.3s ease;
		}

		/* เมื่อเอาเมาส์ชี้เมนู ให้ตัวหนังสือเรืองแสงสีเขียวมิ้นต์ */
		.navbar-dark .navbar-nav .nav-link:hover, 
		.navbar-dark .navbar-nav .nav-link.active {
			color: #34d399; 
			text-shadow: 0 0 12px rgba(52, 211, 153, 0.6);
		}

		/* ปุ่ม Login ล้ำยุค (โดดเด่น เรืองแสง เชิญชวนให้กด) */
		.btn-cyber-login {
			background: linear-gradient(45deg, #10b981, #0ea5e9);
			color: #ffffff !important;
			border: none;
			border-radius: 30px;
			padding: 8px 26px;
			font-weight: 600;
			box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
			transition: all 0.3s ease;
		}
		.btn-cyber-login:hover {
			box-shadow: 0 0 25px rgba(14, 165, 233, 0.7);
			transform: translateY(-2px);
		}

		/* ปุ่ม Logout แบบเส้นขอบนีออน */
		.btn-cyber-logout {
			background: rgba(15, 23, 42, 0.4);
			color: #34d399 !important;
			border: 1px solid #34d399;
			border-radius: 30px;
			padding: 8px 24px;
			font-weight: 600;
			box-shadow: inset 0 0 10px rgba(52, 211, 153, 0.1);
			transition: all 0.3s ease;
		}
		.btn-cyber-logout:hover {
			background: #34d399;
			color: #0f172a !important;
			box-shadow: 0 0 20px rgba(52, 211, 153, 0.6);
			transform: translateY(-2px);
		}

        .page-title { font-size: 32px; font-weight: 600; }
        .page-sub { font-size: 18px; color: #3A4F46; }

        /* search + filter toolbar */
        .toolbar {
            background: #fff;
            border: 2px solid var(--line);
            border-radius: 18px;
            padding: 18px 22px;
            margin-bottom: 28px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.03);
        }
        .search-box {
            position: relative;
            flex: 1 1 280px;
        }
        .search-box input[type="text"] {
            width: 100%;
            border: 2px solid var(--line);
            border-radius: 12px;
            padding: 12px 46px 12px 16px;
            font-size: 17px;
            font-family: 'Kanit', sans-serif;
            outline: none;
        }
        .search-box input[type="text"]:focus {
            border-color: var(--forest);
        }
        .search-box button {
            position: absolute;
            right: 6px; top: 50%; transform: translateY(-50%);
            background: var(--forest);
            border: none; color: #fff;
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .search-box button:hover { background: var(--forest-dark); }

        .filter-chips { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .filter-chip {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 999px;
            border: 2px solid var(--forest);
            color: var(--forest-dark);
            font-size: 16px; font-weight: 600;
            text-decoration: none;
            background: #fff;
            transition: all .15s ease;
        }
        .filter-chip:hover { background: #EAF5EF; color: var(--forest-dark); }
        .filter-chip.active { background: var(--forest); color: #fff; }

        .no-result { text-align:center; padding: 40px 20px; color:#5B6D64; font-size:18px; }

        /* single-column, high-contrast article rows */
        .article-list { display: flex; flex-direction: column; gap: 24px; }
        .article-card {
            background: #fff;
            border: 2px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .article-card .row.g-0 { align-items: stretch; }
        .article-visual { height: 100%; min-height: 260px; object-fit: cover; width: 100%; background:#e6f2eb; }
        .article-visual-svg { width: 100%; height: 100%; min-height: 260px; display:block; }
        @media (max-width: 767px) {
            .article-visual, .article-visual-svg { height: 240px; min-height: 240px; }
        }

        .article-title { color: var(--forest-dark); font-weight: 600; font-size: 23px; line-height: 1.5; }
        .article-preview {
            font-size: 18px;
            color: #33473F;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .badge-disease {
            font-size: 16px; font-weight: 700;
            background: #DCEEE3; color: var(--forest-dark);
            padding: 6px 14px; border-radius: 8px;
        }

        .btn-health {
            background: var(--forest);
            border: none; color: #fff; border-radius: 12px;
            font-size: 18px; font-weight: 700;
            padding: 14px 28px; min-height: 52px;   /* large tap target */
        }
        .btn-health:hover { color: #fff; background: var(--forest-dark); }

        .health-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.05); padding: 34px;
            font-size: 18px; line-height: 1.8;
        }
        .health-card h2 { font-size: 28px; }
        .article-hero-img {
            width: 100%;
            max-height: 480px;
            object-fit: cover;
            display: block;
        }
        .health-card p a,
        .article-preview a {
            color: var(--forest-dark);
            font-weight: 600;
            text-decoration: underline;
            word-break: break-all;
        }
        .health-card p a:hover,
        .article-preview a:hover {
            color: var(--forest);
        }
    </style>
</head>
<body>

<?php
require_once('class.common.php');
$app = new common();
$app->menu();   // navbar ตัวเดียวกับที่ index.php ใช้ ดึงเมนู/สถานะ login จริงจากฐานข้อมูล
?>

<?php if ($task == 'view' && $id > 0): ?>

    <?php
        // ดึงบทความเดี่ยว พร้อมชื่อโรคจากตาราง disease
        $sql = "select `postprint`.*, `disease`.`name` as `disease_name`
                from `postprint`
                left join `disease` on `postprint`.`type` = `disease`.`id`
                where `postprint`.`id` = '".$id."' and `postprint`.`status` = '1'";
        $res = $conn->query($sql);
        $cdr = $res->fetch();
    ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!$cdr): ?>
                    <div class="health-card text-center">
                        <p>ไม่พบบทความที่ต้องการ</p>
                        <a href="public_article.php" class="btn btn-secondary">กลับหน้ารายการ</a>
                    </div>
                <?php else: ?>
                    <?php
                        $name    = htmlspecialchars($cdr['name']);
                        $detail  = auto_link_escaped(nl2br(htmlspecialchars($cdr['detail'])));
                        $picture = htmlspecialchars($cdr['picture']);
                        $disease = htmlspecialchars($cdr['disease_name'] ?? '');
                    ?>
                    <div class="health-card">
                        <?php if (!empty($disease)): ?>
                            <span class="badge-disease d-inline-block mb-3"><?php echo $disease; ?></span>
                        <?php endif; ?>
                        <h2 class="article-title"><?php echo $name; ?></h2>
                        <hr>
                        <?php if (!empty($picture)): ?>
                            <img src="uploads/<?php echo $picture; ?>" class="article-hero-img rounded mb-3" alt="<?php echo $name; ?>">
                        <?php endif; ?>
                        <p><?php echo $detail; ?></p>
                        <a href="public_article.php" class="btn btn-secondary mt-3">
                            &laquo; กลับหน้ารายการบทความ
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php else: ?>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center mb-4">
                <i class="fa-solid fa-book-open fa-3x mb-3" style="color:#2ea36b;"></i>
                <h2 class="page-title article-title">บทความให้ความรู้</h2>
                <p class="page-sub">ความรู้เรื่องโรคและโภชนาการ สำหรับทุกคน</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">

                <!-- ช่องค้นหา + ปุ่มฟิลเตอร์โรค -->
                <div class="toolbar">
                    <form method="get" action="public_article.php" class="d-flex flex-wrap gap-3">
                        <?php if ($type_id > 0): ?>
                            <input type="hidden" name="type" value="<?php echo $type_id; ?>">
                        <?php endif; ?>
                        <div class="search-box">
                            <input type="text" name="q" placeholder="ค้นหาบทความ"
                                   value="<?php echo htmlspecialchars($q); ?>">
                            <button type="submit" aria-label="ค้นหา">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </form>

                    <?php
                        // ดึงรายชื่อโรคที่มีบทความอยู่จริง เพื่อสร้างปุ่มฟิลเตอร์
                        $sql_diseases = "select distinct `disease`.`id`, `disease`.`name`
                                          from `postprint`
                                          left join `disease` on `postprint`.`type` = `disease`.`id`
                                          where `postprint`.`status` = '1' and `disease`.`id` is not null
                                          order by `disease`.`name` asc";
                        $res_diseases = $conn->query($sql_diseases);

                        // ใช้สร้างลิงก์ที่คงค่าคำค้นหาเดิมไว้เวลาสลับฟิลเตอร์
                        $q_param = $q !== '' ? '&q=' . urlencode($q) : '';
                    ?>
                    <div class="filter-chips">
                        <a href="public_article.php?<?php echo ltrim($q_param, '&'); ?>"
                           class="filter-chip <?php echo $type_id === 0 ? 'active' : ''; ?>">
                            ทั้งหมด
                        </a>
                        <?php while ($drow = $res_diseases->fetch()): ?>
                            <a href="public_article.php?type=<?php echo (int)$drow['id']; ?><?php echo $q_param; ?>"
                               class="filter-chip <?php echo $type_id === (int)$drow['id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($drow['name']); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="article-list">
                <?php
                    $where = "`postprint`.`status` = '1'";

                    if ($type_id > 0) {
                        $where .= " and `postprint`.`type` = '" . $type_id . "'";
                    }
                    if ($q !== '') {
                        $q_esc = esc_sql($conn, $q);
                        $where .= " and `postprint`.`name` like '%" . $q_esc . "%'";
                    }

                    $sql = "select `postprint`.*, `disease`.`name` as `disease_name`
                            from `postprint`
                            left join `disease` on `postprint`.`type` = `disease`.`id`
                            where " . $where . "
                            order by `postprint`.`id` desc";
                    $res = $conn->query($sql);
                    $has_result = false;
                    while ($cdr = $res->fetch()):
                        $has_result = true;
                        $pid     = (int)$cdr['id'];
                        $name    = htmlspecialchars($cdr['name']);
                        $picture = htmlspecialchars($cdr['picture']);
                        $disease = htmlspecialchars($cdr['disease_name'] ?? '');
                        $preview = strip_tags($cdr['detail']);
                        $theme   = health_fallback_theme($disease);
                ?>
                    <div class="article-card">
                        <div class="row g-0">
                            <div class="col-md-5">
                                <?php if (!empty($picture)): ?>
                                    <img src="uploads/<?php echo $picture; ?>" alt="<?php echo $name; ?>" class="article-visual">
                                <?php else: ?>
                                    <?php echo render_health_svg($theme); ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <div class="card-body p-4">
                                    <?php if (!empty($disease)): ?>
                                        <span class="badge-disease d-inline-block mb-2"><?php echo $disease; ?></span>
                                    <?php endif; ?>
                                    <h5 class="article-title mb-2"><?php echo $name; ?></h5>
                                    <p class="article-preview mb-3"><?php echo htmlspecialchars($preview); ?></p>
                                    <a href="public_article.php?task=view&id=<?php echo $pid; ?>" class="btn btn-health">
                                        อ่านต่อ <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if (!$has_result): ?>
                    <div class="no-result">
                        <i class="fa-solid fa-circle-info fa-2x mb-2" style="color:#9db3a8;"></i>
                        <p class="mb-0">ไม่พบบทความที่ตรงกับการค้นหา</p>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>