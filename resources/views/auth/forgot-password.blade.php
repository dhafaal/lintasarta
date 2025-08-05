@if (session('status'))
    <p>{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="email" placeholder="Enter your email">
    <button type="submit">Send Reset Link</button>
</form>
