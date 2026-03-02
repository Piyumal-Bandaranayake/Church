<?php
include 'includes/header.php';
include 'includes/db.php';

// Fetch all approved reviews
$stmt = $pdo->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY id DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pt-32 pb-24 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16 reveal reveal-up">
            <h2 class="text-blue-600 font-bold text-sm tracking-uppercase uppercase mb-2">Community Stories</h2>
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">All Success Testimonies</h1>
            <p class="text-gray-500 max-w-2xl mx-auto text-lg">Every journey is unique. Browse through the beautiful stories of faith and love from our blessed couples.</p>
        </div>

        <?php if (empty($reviews)): ?>
            <div class="text-center py-20 bg-white rounded-[3rem] shadow-sm border border-gray-100">
                <p class="text-gray-400 italic text-lg">No testimonies have been shared yet. Be the first!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($reviews as $review): ?>
                <div onclick="openTestimonyModal(<?php echo htmlspecialchars(json_encode($review)); ?>)" class="group bg-white rounded-[2.5rem] p-8 border border-gray-100 hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-500 relative flex flex-col h-full cursor-pointer">
                    <!-- Quote Icon -->
                    <div class="absolute top-8 right-8 text-blue-50 group-hover:text-blue-100 transition-colors">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                    </div>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-lg transform group-hover:scale-110 transition-transform duration-500">
                            <?php $review_img = !empty($review['image1']) ? $review['image1'] : 'https://via.placeholder.com/150?text=Couple'; ?>
                            <img src="<?php echo htmlspecialchars($review_img); ?>" alt="Couple" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($review['name']); ?></h4>
                            <div class="flex gap-0.5 mt-1 text-yellow-400">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <p class="text-gray-600 leading-relaxed italic relative z-10 line-clamp-4 pb-4">
                            "<?php echo nl2br(htmlspecialchars($review['description'])); ?>"
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal from index.php (simplified or included) -->
<?php include 'includes/testimony_modal.php'; ?>

<?php include 'includes/footer.php'; ?>
