{
	"environment": "debug",
	"version": "1",
	"debug":
	{
		"localcache": "none",
		"profiler": "none"
	},
	"test":
	{
		"config_map":
		{
			"db": "db/test"
		},
		"localcache": "none",
		"profiler": "none"
	},
	"production":
	{
		"localcache": "apc",
		"profiler": "none"
	}
}