SELECT * 
    FROM (
        (
            SELECT Afschriftnummer,change_date 
            FROM VoorlopigeRekeningafschriften  
            WHERE VoorlopigeRekeningafschriften.Rekening = '{rekeningNr}'
            AND YEAR(VoorlopigeRekeningafschriften.Datum) = '{stampYear}' 
            ORDER BY Afschriftnummer DESC 
            LIMIT 1
        )
        UNION (
            SELECT Afschriftnummer,change_date 
            FROM Rekeningafschriften  
            WHERE Rekeningafschriften.Rekening = '{rekeningNr}' 
            AND YEAR(Rekeningafschriften.Datum) = '{stampYear}'
            ORDER BY Afschriftnummer DESC 
            LIMIT 1
        ) 
    ) 
    as tmp 
    order by change_date desc 
    limit 1