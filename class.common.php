<?php

class common
{

	function init()
	{
		$this->menu();
		$this->body();
	}

	function menu()
	{
		$conn = new connect();
		$uid = $_SESSION['uid'] ?? 4;
		?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-cyber-health sticky-top py-2">
            <div class="container-fluid">
                <a class="navbar-brand brand-glow" href="index.php">
                    <i class="bi bi-activity"></i> Bon_Appetit
                </a>
				<a class="navbar-brand brand-glow" href="index.php">
                    Food
                </a>
				<a class="navbar-brand brand-glow" href="public_article.php">
                    ข้อมูลโรค
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ps-lg-4">

						<?php
							$sql = "select `m`.`name` as `name`, `m`.`id` as `id`, (select count(`menu`.`id`) from `menu`, `app`, `acl`, `uig` where `menu`.`status` = '1' and `app`.`status` = '1' and `acl`.`status` = '1' and `uig`.`status` = '1' and `app`.`id` = `menu`.`app_id` and `acl`.`appid` = `app`.`id` and `acl`.`ugid` = `uig`.`ugid` and `menu`.`root` = `m`.`id` and `uig`.`uid` = '".$_SESSION['uid']."') as `cc` from `menu` as `m` where `m`.`status` = '1' and `m`.`root` = '0' having `cc` > 0 order by `m`.`ord`";
							$res = $conn->query($sql);

							while ($cdr = $res->fetch())
							{
								$menu_name = strtolower(trim($cdr['name']));
								$hidden_menus = ['systems', 'meal plan'];
								if (in_array($menu_name, $hidden_menus))
								{
									continue;
								}
								?>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<?php
										echo $cdr['name'];
									?>
									</a>
									<ul class="dropdown-menu">
									<?php
										$sqla = "select `app`.`dir` as `dir`, `menu`.`name` as `name`, `menu`.`func` as `func`, `menu`.`param` as `param` from `menu`, `app`, `acl`, `uig` where `menu`.`status` = '1' and `app`.`status` = '1' and `acl`.`status` = '1' and `uig`.`status` = '1' and `app`.`id` = `menu`.`app_id` and `acl`.`appid` = `app`.`id` and `acl`.`ugid` = `uig`.`ugid` and `menu`.`root` = '".$cdr['id']."' and `uig`.`uid` = '".$_SESSION['uid']."' group by `menu`.`id` order by `menu`.`ord`";
										$resa = $conn->query($sqla);
										while ($cdra = $resa->fetch())
										{
											echo "<li>";
											echo '<a class="dropdown-item" href="index.php?option='.$cdra['dir'].'&task='.$cdra['func'].$cdra['param'].'">';
											echo $cdra['name'];
											echo '</a>';
											echo "</li>";
										}
									?>
									</ul>
								</li>
								<?php
							}
							?>
                    </ul>

					<style>
                        .profile-dropdown-card 
                        {
                            border: none;
                            border-radius: 16px;
                            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
                            min-width: 280px;
                            padding: 24px 20px 20px;
                            margin-top: 12px !important;
                        }
                        .profile-avatar-icon 
                        {
                            color: #1b6d46; 
                            font-size: 4.5rem;
                            line-height: 1;
                            margin-bottom: 10px;
                        }
                        .profile-name 
                        {
                            font-weight: 700;
                            color: #212529;
                            font-size: 1.05rem;
                            margin-bottom: 10px;
                        }
                        .badge-health 
                        {
                            background-color: #d1e7dd;
                            color: #1b6d46;
                            border: 1px solid #1b6d46;
                            font-weight: 700;
                            padding: 6px 18px;
                            font-size: 0.85rem;
                        }
                        .profile-divider 
                        {
                            border-top: 1px solid #e2e8f0;
                            margin: 20px 0;
                            opacity: 1;
                        }
                        .menu-link-item 
                        {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            padding: 12px 4px;
                            text-decoration: none;
                            color: #334155;
                            transition: all 0.2s;
                            border-radius: 8px;
                        }
                        .menu-link-item:hover 
                        {
                            background-color: #f8fafc;
                            transform: translateX(4px);
                        }
                        .menu-link-item-left 
                        {
                            display: flex;
                            align-items: center;
                        }
                        .menu-link-item-left .icon-left 
                        {
                            color: #0ea5e9; 
                            font-size: 1.3rem;
                            margin-right: 14px;
                            width: 24px;
                            text-align: center;
                        }
                        .menu-link-item-left .text-main 
                        {
                            font-weight: 600;
                            font-size: 0.95rem;
                        }
                        .menu-link-item .icon-right 
                        {
                            color: #94a3b8;
                            font-size: 0.85rem;
                        }
                        .btn-logout-modern 
                        {
                            background-color: #f8fafc; 
                            color: #10b981 !important; 
                            border: 2px solid #10b981;
                            border-radius: 50px;
                            padding: 10px 24px;
                            font-size: 1.1rem;
                            font-weight: 700;
                            display: inline-flex;
                            justify-content: center;
                            align-items: center;
                            transition: all 0.3s ease;
                            text-decoration: none;
                            width: 100%; 
                        }
                        .btn-logout-modern i 
                        {
                            margin-right: 8px;
                            font-size: 1.3rem;
                        }
                        .btn-logout-modern:hover 
                        {
                            background-color: #10b981;
                            color: #ffffff !important;
                            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
                        }
                        .btn-login-modern 
                        {
                            font-family: 'Prompt', sans-serif;
                            background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
                            color: #ffffff !important;
                            border: none;
                            border-radius: 12px;
                            padding: 8px 24px;
                            font-size: 1.15rem;
                            font-weight: 700;
                            display: inline-flex;
                            align-items: center;
                            box-shadow: 0 4px 15px rgba(21, 128, 61, 0.25);
                            transition: all 0.3s ease;
                            text-decoration: none;
                        }
                        .btn-login-modern i 
                        {
                            color: #ffffff;
                            margin-right: 10px;
                            font-size: 1.3rem;
                        }
                        .btn-login-modern:hover 
                        {
                            transform: translateY(-2px);
                            box-shadow: 0 6px 20px rgba(21, 128, 61, 0.4);
                            background: linear-gradient(135deg, #16a34a 0%, #166534 100%);
                            color: #ffffff !important;
                        }
                        .nav-profile-toggle 
                        {
                            color: #0ea5e9 !important;
                            font-weight: 700;
                            display: inline-flex;
                            align-items: center;
                            gap: 0.35rem;
                        }
                        .nav-item.dropdown 
                        {
                            display: flex;
                            align-items: center;
                        }
                        .nav-item.dropdown .dropdown-toggle::after 
                        {
                            margin-left: 0.35rem;
                        }
                    </style>

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
						<?php
						if ($uid != 4) 
                        { 
							?>
							<li class="nav-item dropdown ms-lg-3">
								<a class="nav-link dropdown-toggle d-flex align-items-center nav-profile-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
						        <i class="bi bi-person-circle fs-5 me-2"></i> <?php echo htmlspecialchars($_SESSION['user'] ?? 'My Profile'); ?>
								
								</a>
							<div class="dropdown-menu dropdown-menu-end profile-dropdown-card">
									<div class="text-center">
										<i class="bi bi-person-hearts profile-avatar-icon d-block"></i>
											<div class="profile-name">
												<?php echo htmlspecialchars($_SESSION['user'] ?? 'My Profile'); ?>
											</div>
										<a class="menu-link-item" href="index.php?option=history&task=def&id=<?php echo htmlentities($_SESSION['uid'] ?? 4); ?>">
											<div class="menu-link-item-left">
												<i class="bi bi-calendar-check icon-left"></i>
												<span class="text-main">ประวัติการจัดตาราง</span>
											</div>
											<i class="bi bi-chevron-right icon-right"></i>
										</a>
										
                                        <a class="menu-link-item" href="index.php?option=profile&task=def&id=<?php echo htmlentities($_SESSION['uid'] ?? 4); ?>">
											<div class="menu-link-item-left">
												<i class="bi bi-gear icon-left"></i>
												<span class="text-main">ตั้งค่าบัญชี</span>
											</div>
											<i class="bi bi-chevron-right icon-right"></i>
										</a>

										<a class="menu-link-item" href="index.php?option=mis&task=def&id=<?php echo htmlentities($_SESSION['uid'] ?? 4); ?>">
											<div class="menu-link-item-left">
												<i class="bi bi-gear icon-left"></i>
												<span class="text-main">ภารกิจ</span>
											</div>
											<i class="bi bi-chevron-right icon-right"></i>
										</a>
									</div>

									<a class="btn-logout-modern" href="index.php?option=logs&task=logout" id="logout">
                                        <i class="bi bi-power"></i> Logout
                                    </a>
								</div>
							</li>
							<?php
						}
						else 
						{
							?>
							<li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                                <a class="btn-login-modern" href="index.php?option=logs&task=login_form">
                                    <i class="bi bi-fingerprint"></i> Login
                                </a>
                            </li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</nav>
		<?php

	}

	function body()
	{
		$conn = new connect();
		$acl = $conn->check_acl();
		if ($acl > 0)
		{
			$opt = $_SESSION['option'];
			$tas = $_SESSION['task'];
			require_once('class.'.$opt.'.php');
			$class = new $opt();
			$class->$tas();
		}
		else
		{
		?>
		<div id='headers' class='container-fluid'>
			<div class='row'>
				<div class='col-12 text-center'>
					<h1 class='text-danger'>
						You don't have permission
						<br />
						Please contact admin for service
					</h1>
				</div>
			</div>
		</div>
		<?php
		}
	}


}

?>