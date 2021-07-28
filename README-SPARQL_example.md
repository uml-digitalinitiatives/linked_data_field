Bootstrapping a SPARQL query for Linked Data Field

There are many places with example SPARQL queries that you can take and modify for your needs. The tricky part is to get the particular variables to represent the label and URI portions.

Let's use the Getty Art and Architecture Thesaurus endpoint as an example.

http://vocab.getty.edu/sparql

There's an interactive endpoint at that address, that is also the URL to use as the endpoint in a Linked Cata Endpoint configuration.

There's an extensive guide to how to use the resource here:

http://vocab.getty.edu/doc/

And many useful real-world examples here, but these are notably not very easy to intuitively parse as a SPARQL novice.

http://vocab.getty.edu/doc/queries/

Notably, section 2.8 looks useful to us, "Case-insensitive Full Text Search Query"

> In auto-complete applications you'd often want to treat the user input in a case-insensitive way. The FTS index luc:term includes all terms lower-cased and stemmed, so it already takes care of filtering. But you'd also want to sort the results case-insensitively. Assuming you want to limit to AAT concepts only (not hierarchies or guide terms), here's an appropriate query (compared to the previous section, we omit Descr, Type and ExtraType):
```
select ?Subject ?Term ?Parents ?ScopeNote {
  ?Subject a skos:Concept; luc:term "gold"; skos:inScheme aat: ;
     gvp:prefLabelGVP [xl:literalForm ?Term].
  optional {?Subject gvp:parentStringAbbrev ?Parents}
  optional {?Subject skos:scopeNote [dct:language gvp_lang:en; rdf:value ?ScopeNote]}
} order by asc(lcase(str(?Term)))
```
> We need to strip the language tag from ?Term by using the str() function, since order is undefined for literals with language tag, e.g. the relative order is undefined for "a"@en_GB and "b"@en_GB (two literals with the same language tag).
Thanks to Athanasios Velios for providing the inspiration for this query.

This is a well-constructed query for the string "gold" that does a few things we don't need, so we can strip it out. But let's start by pasting this query into the box at the Getty endpoint and submit the form to see what it does and to start from a working example.

[Here are the results](http://vocab.getty.edu/sparql?query=select+%3FSubject+%3FTerm+%3FParents+%3FScopeNote+%7B%0D%0A++%3FSubject+a+skos%3AConcept%3B+luc%3Aterm+%22gold%22%3B+skos%3AinScheme+aat%3A+%3B%0D%0A+++++gvp%3AprefLabelGVP+%5Bxl%3AliteralForm+%3FTerm%5D.%0D%0A++optional+%7B%3FSubject+gvp%3AparentStringAbbrev+%3FParents%7D%0D%0A++optional+%7B%3FSubject+skos%3AscopeNote+%5Bdct%3Alanguage+gvp_lang%3Aen%3B+rdf%3Avalue+%3FScopeNote%5D%7D%0D%0A%7D+order+by+asc%28lcase%28str%28%3FTerm%29%29%29%0D%0A&_implicit=false&implicit=true&_equivalent=false&_form=%2Fsparql)

The HTML results are in a table. The JSON format is what Linked Data Field parses, so click on the 'Download as JSON' link at the top right of the results page.

```json
{
  "head" : {
    "vars" : [ "Subject", "Term", "Parents", "ScopeNote" ]
  },
  "results" : {
    "bindings" : [ {
      "Subject" : {
        "type" : "uri",
        "value" : "http://vocab.getty.edu/aat/300206175"
      },
      "Term" : {
        "xml:lang" : "en",
        "type" : "literal",
        "value" : "aventurine glass"
      },
      "Parents" : {
        "type" : "literal",
        "value" : "<glass by technique>, glass (material), ... Materials Facet"
      },
      "ScopeNote" : {
        "xml:lang" : "en",
        "type" : "literal",
        "value" : "Synthetic imitation of aventurine quartz crystals. Goldstone is made by embedding metallic flakes in quartz glass. It is used in costume jewelry. It is a translucent glass flecked with metallic particles to imitate the appearance of brownish aventurine quartz. "
      }
    },
```

The parts of a result set we need are a URI for a canonical identifier, and a human-readable label.

We don't need the parents or the scope note so we can remove those parts from the example query:

```
select ?Subject ?Term {
  ?Subject a skos:Concept; luc:term "gold"; skos:inScheme aat: ;
     gvp:prefLabelGVP [xl:literalForm ?Term].
} order by asc(lcase(str(?Term)))
```
Giving us a simpler result set:

```json
{
  "head" : {
    "vars" : [ "Subject", "Term" ]
  },
  "results" : {
    "bindings" : [ {
      "Subject" : {
        "type" : "uri",
        "value" : "http://vocab.getty.edu/aat/300206175"
      },
      "Term" : {
        "xml:lang" : "en",
        "type" : "literal",
        "value" : "aventurine glass"
      }
    }, {
```

We have a couple of problems to overcome, most obviously that we don't have a human-readable label in these results, so we will need to go find one. Secondly, the results themselves are JSON objects and we just need the string itself.

SPARQL is powerful in that it lets you explore an object by asking questions about it. Let's just get every property of one particular object.

```sparql
SELECT ?s ?p ?o WHERE {
  <http://vocab.getty.edu/aat/300343821> ?p ?o
}
```

[Here are the results](http://vocab.getty.edu/sparql?query=++SELECT+%3Fs+%3Fp+%3Fo+WHERE+%7B+%3Chttp%3A%2F%2Fvocab.getty.edu%2Faat%2F300343821%3E+%3Fp+%3Fo+%7D%0D%0A&_implicit=false&implicit=true&_equivalent=false&_form=%2Fsparql).

The standard key for a label in rdf is http://www.w3.org/2000/01/rdf-schema#label, which is usually shortened via a namespace to rdfs:label.

We can add this to the original query like this:

```sparql
select ?Subject ?Term ?Label{
?Subject a skos:Concept; luc:term "canada"; skos:inScheme aat: ; .

?Subject rdfs:label ?Label
} order by asc(lcase(str(?Term)))
````

Finally, where "canada" is hard-coded above, replace it with "@input" so the module knows that is where it should substitute the string that a user is typing in to the field for lookup.```sparql
select ?Subject ?Term ?Label{
?Subject a skos:Concept; luc:term "@input"; skos:inScheme aat: ; .

?Subject rdfs:label ?Label
} order by asc(lcase(str(?Term)))
````

Place this query in to the "SPARQL Query" configuration field, "?Label" into the Label field and "?Subject" into the URL field and then save the form, and you will have a working endpoint.


