@if (session('pdf_key'))
  @php
    $pdfContent = \Illuminate\Support\Facades\Cache::pull(session('pdf_key'));
  @endphp

  @if ($pdfContent)
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const base64Data = @json($pdfContent);
        const fileName = @json(session('pdf_name'));

        const link = document.createElement('a');
        link.href = 'data:application/pdf;base64,' + base64Data;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      });
    </script>
  @endif
@endif