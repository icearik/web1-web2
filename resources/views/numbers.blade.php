<x-layout>
  <div class="container">
    <h1>Interesting Number Facts</h1>
    <form method="POST">
      @csrf
      <div class="form-group">
        <label for="number">Enter a number:</label>
        <input type="text" name="number" id="number" class="form-control" required>
	@error('number')
          <div>{{ $message }}</div>
        @enderror
      </div>
      <button type="submit" class="btn btn-primary">Get Fact</button>
    </form>

    @isset($fact)
      <div class="fact-container mt-4">
        <p>{{ $fact }}</p>
      </div>
    @endisset

    @isset($errorMessage)
      <div class="alert alert-danger mt-4">
        {{ $errorMessage }}
      </div>
    @endisset
  </div>
</x-layout>

