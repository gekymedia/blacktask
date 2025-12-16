@props(['id' => 'notification', 'message' => ''])

<div 
    id="{{ $id }}" 
    class="notification fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden z-50">
    {{ $message }}
</div>

<style>
.notification {
    transition: all 0.3s ease;
    transform: translateY(20px);
    opacity: 0;
}

.notification.show {
    transform: translateY(0);
    opacity: 1;
}
</style>

