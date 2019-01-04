select item_master.NAME,count(VOTE_DATE)
from votes_active
	left join item_master on votes_active.ITEM_ID=item_master.ITEM_ID
group by NAME
order by count(VOTE_DATE) desc
LIMIT 6;