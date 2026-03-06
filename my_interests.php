<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Auto-migration check
try {
    $pdo->query("SELECT 1 FROM interests LIMIT 1");
} catch (Exception $e) {
    include_once 'setup_db.php';
}

$user_id = $_SESSION['user_id'];

// Fetch Received Interests (People interested in me)
$received_stmt = $pdo->prepare("
    SELECT i.*, c.fullname, c.reg_number, c.photo_path, c.hometown, c.occupation, c.age, c.my_phone, c.parent_phone
    FROM interests i
    JOIN candidates c ON i.sender_id = c.id
    WHERE i.receiver_id = ?
    ORDER BY i.created_at DESC
");
$received_stmt->execute([$user_id]);
$received_interests = $received_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Sent Interests (People I am interested in)
$sent_stmt = $pdo->prepare("
    SELECT i.*, c.fullname, c.reg_number, c.photo_path, c.hometown, c.occupation, c.age
    FROM interests i
    JOIN candidates c ON i.receiver_id = c.id
    WHERE i.sender_id = ?
    ORDER BY i.created_at DESC
");
$sent_stmt->execute([$user_id]);
$sent_interests = $sent_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current user details
$me_stmt = $pdo->prepare("SELECT reg_number, fullname FROM candidates WHERE id = ?");
$me_stmt->execute([$user_id]);
$my_details = $me_stmt->fetch(PDO::FETCH_ASSOC);

?>
<?php include 'includes/header.php'; ?>

<main class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 themed-background">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6 reveal reveal-up">
            <div>
                <h1 class="text-4xl font-black text-primary mb-2">My Interests (මගේ උනන්දුවීම්)</h1>
                <p class="text-gray-500 font-medium">Manage your requests and connections (ඔබගේ ඉල්ලීම් සහ සම්බන්ධතා කළමනාකරණය කරන්න)</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="my_profile.php" class="px-6 py-3 bg-white text-gray-700 font-bold rounded-2xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-all text-sm">
                    Dashboard (පුවරුව)
                </a>
                <a href="candidates.php" class="px-6 py-3 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/20 hover:bg-primary-hover transition-all text-sm">
                    Browse More (තව සොයන්න)
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            <!-- Section 1: Received Requests -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900">Received Requests (ලැබුණු ඉල්ලීම්)</h2>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold"><?php echo count($received_interests); ?></span>
                </div>

                <?php if (empty($received_interests)): ?>
                    <div class="bg-white/50 backdrop-blur-sm p-12 rounded-[2rem] border-2 border-dashed border-gray-200 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 5-8-5" /></svg>
                        </div>
                        <p class="text-gray-500 font-bold uppercase text-[10px] tracking-widest">No requests yet (තවමත් ඉල්ලීම් නැත)</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($received_interests as $item): ?>
                        <div class="interest-card group bg-white p-5 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-500 reveal reveal-up">
                            <div class="flex items-start gap-4">
                                <div class="w-20 h-24 rounded-2xl overflow-hidden shadow-md flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($item['photo_path'] ?: 'https://via.placeholder.com/200x300'); ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="px-2 py-0.5 bg-primary/5 text-primary rounded-full text-[9px] font-black tracking-widest uppercase">#<?php echo $item['reg_number']; ?></span>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase"><?php echo date('M d', strtotime($item['created_at'])); ?></span>
                                    </div>
                                    <h3 class="text-lg font-black text-gray-900 truncate"><?php echo htmlspecialchars($item['fullname']); ?></h3>
                                    <p class="text-xs text-gray-500 font-medium"><?php echo $item['age']; ?> yrs (වයස) · <?php echo htmlspecialchars($item['hometown']); ?> (ගම)</p>
                                    
                                    <!-- Status Badge -->
                                    <div class="mt-3 flex items-center gap-2">
                                        <?php if ($item['status'] === 'accepted'): ?>
                                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Accepted (පිළිගත්තා)
                                            </span>
                                        <?php elseif ($item['status'] === 'rejected'): ?>
                                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Declined (ප්‍රතික්ෂේප කළා)
                                            </span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Pending (අපේක්ෂිතයි)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="grid grid-cols-2 gap-3 mt-5">
                                <?php if ($item['status'] === 'pending'): ?>
                                    <button 
                                        onclick="handleInterest(this, <?php echo $item['id']; ?>, 'accept')" 
                                        data-sender-phone="<?php echo htmlspecialchars($item['parent_phone'] ?: $item['my_phone']); ?>"
                                        data-sender-name="<?php echo htmlspecialchars($item['fullname']); ?>"
                                        data-sender-reg="<?php echo htmlspecialchars($item['reg_number']); ?>"
                                        class="py-3 bg-green-500 text-white font-bold rounded-2xl hover:bg-green-600 transition-all text-[11px] shadow-lg shadow-green-500/20 active:scale-95 uppercase tracking-wider">
                                        Accept (පිළිගන්න)
                                    </button>
                                    <button onclick="handleInterest(this, <?php echo $item['id']; ?>, 'reject')" class="py-3 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-red-50 hover:text-red-500 transition-all text-[11px] active:scale-95 uppercase tracking-wider">
                                        Decline (ප්‍රතික්ෂේප කරන්න)
                                    </button>
                                <?php else: ?>
                                    <a href="profile.php?id=<?php echo $item['sender_id']; ?>" class="col-span-2 py-3 bg-primary text-white font-bold rounded-2xl hover:bg-primary-hover transition-all text-[11px] text-center shadow-lg shadow-primary/20 uppercase tracking-wider">
                                        View Profile (ප්‍රෝෆයිලය බලන්න)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Section 2: Sent Requests -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900">Sent Interests (යැවූ ඉල්ලීම්)</h2>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold"><?php echo count($sent_interests); ?></span>
                </div>

                <?php if (empty($sent_interests)): ?>
                    <div class="bg-white/50 backdrop-blur-sm p-12 rounded-[2rem] border-2 border-dashed border-gray-200 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </div>
                        <p class="text-gray-500 font-bold uppercase text-[10px] tracking-widest">You haven't sent any interests (ඔබ තවමත් කිසිවෙකුට කැමැත්ත ප්‍රකාශ කර නැත)</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($sent_interests as $item): ?>
                        <div class="interest-card group bg-white p-5 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-500 reveal reveal-up">
                            <div class="flex items-start gap-4">
                                <div class="w-20 h-24 rounded-2xl overflow-hidden shadow-md flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($item['photo_path'] ?: 'https://via.placeholder.com/200x300'); ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="px-2 py-0.5 bg-primary/5 text-primary rounded-full text-[9px] font-black tracking-widest uppercase">#<?php echo $item['reg_number']; ?></span>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase"><?php echo date('M d', strtotime($item['created_at'])); ?></span>
                                    </div>
                                    <h3 class="text-lg font-black text-gray-900 truncate"><?php echo htmlspecialchars($item['fullname']); ?></h3>
                                    <p class="text-xs text-gray-500 font-medium"><?php echo htmlspecialchars($item['occupation']); ?></p>
                                    
                                    <!-- Status Badge -->
                                    <div class="mt-3 flex items-center gap-2">
                                        <?php if ($item['status'] === 'accepted'): ?>
                                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Accepted (කැමැත්ත ලැබුණා)
                                            </span>
                                        <?php elseif ($item['status'] === 'rejected'): ?>
                                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Declined (ප්‍රතික්ෂේප කළා)
                                            </span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span> Waiting Response (පිළිතුරක් අපේක්ෂාවෙන්)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button onclick="handleInterest(this, <?php echo $item['id']; ?>, 'delete')" class="p-2 text-gray-300 hover:text-red-500 transition-colors" title="Withdraw Interest (අස්කරගන්න)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                            
                            <div class="mt-5">
                                <a href="profile.php?id=<?php echo $item['receiver_id']; ?>" class="block w-full py-3 bg-gray-50 text-gray-700 font-bold rounded-2xl hover:bg-primary hover:text-white transition-all text-[11px] text-center border border-gray-100 uppercase tracking-wider">
                                    View Profile (ප්‍රෝෆයිලය බලන්න)
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<script>
// Current user info for WhatsApp
const myRegNo = '<?php echo $my_details['reg_number']; ?>';
const myName = '<?php echo addslashes($my_details['fullname']); ?>';

function handleInterest(btn, interestId, action) {
    if (action === 'delete' && !confirm('Are you sure you want to withdraw this interest?')) return;
    
    // Loading state
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '...';

    const formData = new FormData();
    formData.append('interest_id', interestId);
    formData.append('action', action);

    fetch('handle_interest_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // WHATSAPP AUTOMATION ON ACCEPT
            if (action === 'accept') {
                const sPhone = btn.getAttribute('data-sender-phone');
                const sName = btn.getAttribute('data-sender-name');
                const sReg = btn.getAttribute('data-sender-reg');

                if (sPhone) {
                    const cleanPhone = sPhone.replace(/\D/g, '');
                    const message = encodeURIComponent(`Hello ${sName} (#${sReg}), your interest has been accepted by ${myName} (#${myRegNo}). Let's discuss further.`);
                    window.open(`https://wa.me/${cleanPhone}?text=${message}`, '_blank');
                }
            }

            // Success feedback
            btn.parentElement.parentElement.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong.');
        btn.disabled = false;
        btn.innerHTML = originalContent;
    });
}
</script>

<?php include 'includes/footer.php'; ?>
