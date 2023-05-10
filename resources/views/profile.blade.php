<x-layout>
  <h1>Enter your zipcode</h1>
  <form method="POST">
    @csrf
    <div>
      <label for="zipcode">Zipcode:</label>
      <input type="text" name="zipcode" id="zipcode" required>
      @error('zipcode')
        <div>{{ $message }}</div>
      @enderror
    </div>
    <button type="submit">Submit</button>
  </form>
</x-layout>

