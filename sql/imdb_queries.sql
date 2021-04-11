-- 1. Names of all movies released in 1995
SELECT name FROM movies
WHERE year = 1995;

-- 2. Number of actors who took part in the movie "Lost in Translation"
--    Option 1. Sub-query
SELECT Count(*) AS actors_count FROM actors
WHERE id IN (
    SELECT actor_id FROM roles WHERE movie_id = (
        SELECT id FROM movies WHERE name = 'Lost in Translation'
    )
);

--    Option 2. Join
SELECT Count(*) AS actors_count FROM actors
INNER JOIN roles  ON actors.id = actor_id
INNER JOIN movies ON movie_id  = movies.id
WHERE name = 'Lost in Translation';

-- 3. Names of all the actors who took a part in the movie "Lost in Translation"
SELECT Concat(first_name, ' ', last_name) AS name FROM actors
INNER JOIN roles  ON actors.id = actor_id
INNER JOIN movies ON movie_id  = movies.id
WHERE name = 'Lost in Translation';

-- 4. Director of the movie "Fight Club"
SELECT Concat(first_name, ' ', last_name) AS name
FROM directors WHERE id = (
    SELECT director_id FROM movies JOIN movies_directors ON id = movie_id
    WHERE  name = 'Fight Club'
);

-- 5. Number of movies directed by Clint Eastwood
SELECT Count(*) FROM movies_directors
WHERE  director_id = (
    SELECT id FROM directors
    WHERE first_name = 'Clint' AND last_name = 'Eastwood'
);

-- 6. Names of movies directed by Clint Eastwood
SELECT name FROM movies
JOIN movies_directors ON id = movie_id
WHERE director_id = (
    SELECT id FROM directors
    WHERE first_name = 'Clint' AND last_name = 'Eastwood'
);

-- 7. Names of directors of Horror films
SELECT Concat(first_name, ' ', last_name) AS name
FROM directors AS d JOIN directors_genres AS dg ON d.id = dg.director_id
WHERE genre = 'Horror';

-- 8. Names of actors that took part in Christopher Nolan's movies
SELECT Concat(first_name, ' ', last_name) AS name, movie_id
FROM actors JOIN roles ON id = actor_id
WHERE movie_id IN (
    SELECT movie_id FROM directors
    JOIN movies_directors ON id = director_id
    WHERE first_name = 'Christopher' AND last_name = 'Nolan'
) GROUP  BY id;