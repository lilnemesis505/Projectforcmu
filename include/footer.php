<?php 
// [เพิ่ม] 
// เพิ่มโค้ด 3 บรรทัดนี้ไว้บนสุด
// เพื่อตรวจสอบว่า $path_prefix ถูกตั้งค่ามาจากไฟล์หลัก (เช่น index.php หรือ ev1.php) หรือไม่
if (!isset($path_prefix)) {
    $path_prefix = '';
}
?>

</div> <footer class="bg-dark text-white pt-5 pb-4 border-top">
    
    <div class="container-fluid" style="background-color: transparent;">
    <div class="row">

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 fw-bold">ติดต่อสอบถาม</h5>
                <p class="mb-1" style="font-size: 0.9rem;">0-5321-7139</p>
                <p class="mb-1" style="font-size: 0.9rem;">สำนักงานใหญ่: จันทร์-ศุกร์ 8.30 - 20.00 น.</p>
                <p class="mb-1" style="font-size: 0.9rem;">สาขาอาคาร 51: เปิด 9.00 - 19.00 น.</p>
                <p class="mb-1" style="font-size: 0.9rem;">cmucoop@gmail.com</p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 fw-bold">ลิงก์ที่เกี่ยวข้อง</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">กระทรวงเกษตรและสหกรณ์</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">กรมตรวจบัญชีสหกรณ์</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">กรมส่งเสริมสหกรณ์</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">สันนิบาตสหกรณ์</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 fw-bold">CMUCOOP'S</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">เกี่ยวกับเรา</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">คณะกรรมการ</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">ดาวน์โหลด</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">แผนที่</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 fw-bold">หมวดหมู่สินค้า</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">เครื่องดื่ม</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">ขนมเบเกอรี่</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">ของใช้ทั่วไป</a></li>
                    <li class="mb-2"><a href="#" class="text-white" style="text-decoration: none; font-size: 0.9rem;">อาหารเครื่องปรุง</a></li>
                </ul>
            </div>
        </div>
        
        <hr class="text-muted"> <div class="text-center text-muted" style="font-size: 0.9rem;">
            <p class="mb-0">
                <a href="<?php echo $path_prefix; ?>admin/auth/login.php" style="text-decoration: none; color: inherit;" title="Admin Login">&copy;</a>
                <?php echo date("Y"); ?> CMU X ACADEMY. All Rights Reserved.
            </p>
        </div>
        </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>