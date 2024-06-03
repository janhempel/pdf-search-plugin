jQuery(document).ready(function($) {
    $('#pdf_search_form').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#pdf_search_term').val();

        // Ladesymbol anzeigen
        $('#pdf_search_results').html('<div id="loading">Suche...</div>');

        $.ajax({
            url: pdf_search_params.ajax_url,
            type: 'POST',
            data: {
                action: 'pdf_search',
                search_term: searchTerm
            },
            success: function(response) {
                // Ladesymbol ausblenden und Ergebnisse anzeigen
                $('#pdf_search_results').html(response);
            }
        });
    });
});
